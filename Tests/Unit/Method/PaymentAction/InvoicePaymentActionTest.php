<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\PaymentAction;

use Oro\Bundle\ApruveBundle\Apruve\Factory\Invoice\ApruveInvoiceFromPaymentContextFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\Invoice\ApruveInvoiceFromResponseFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\ApruveBundle\Client\Request\Invoice\Factory\CreateInvoiceRequestFactoryInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\InvoicePaymentAction;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

class InvoicePaymentActionTest extends AbstractPaymentActionTest
{
    private const APRUVE_ORDER_ID = 'sampleApruveOrderId';
    private const APRUVE_INVOICE_ID = 'sampleApruveInvoiceId';

    protected const RETURN_ERROR = [
        'successful' => false,
        'message' => 'oro.apruve.payment_transaction.invoice.result.error'
    ];

    /** @var CreateInvoiceRequestFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $createInvoiceRequestFactory;

    /** @var ApruveInvoiceFromResponseFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveInvoiceFromResponseFactory;

    /** @var ApruveInvoice|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveInvoice;

    /** @var ApruveInvoiceFromPaymentContextFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $apruveEntityFromPaymentContextFactory;

    /** @var InvoicePaymentAction */
    protected $paymentAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentActionName = InvoicePaymentAction::NAME;

        $this->apruveInvoice = $this->createMock(ApruveInvoice::class);
        $this->apruveEntityFromPaymentContextFactory= $this->createMock(
            ApruveInvoiceFromPaymentContextFactoryInterface::class
        );
        $this->apruveInvoiceFromResponseFactory = $this->createMock(ApruveInvoiceFromResponseFactoryInterface::class);
        $this->createInvoiceRequestFactory = $this->createMock(CreateInvoiceRequestFactoryInterface::class);

        $this->paymentAction = new InvoicePaymentAction(
            $this->paymentContextFactory,
            $this->apruveEntityFromPaymentContextFactory,
            $this->apruveInvoiceFromResponseFactory,
            $this->apruveConfigRestClientFactory,
            $this->createInvoiceRequestFactory
        );

        $this->setUpLoggerMock($this->paymentAction);
    }

    public function testExecute()
    {
        $isSuccessful = true;

        $this->mockPaymentContextFactory($this->paymentContext);
        $this->mockApruveInvoiceFactory();
        $this->mockSourcePaymentTransaction(PaymentMethodInterface::AUTHORIZE, self::APRUVE_ORDER_ID);
        $this->mockApruveInvoiceRequest(self::REQUEST_DATA, self::APRUVE_ORDER_ID);
        $this->mockApruveRestClient($this->mockRestResponse($isSuccessful, self::RESPONSE_DATA));

        $this->paymentTransaction->expects(self::once())
            ->method('setResponse')
            ->with(self::RESPONSE_DATA)
            ->willReturnSelf();

        $this->paymentTransaction->expects(self::once())
            ->method('setReference')
            ->with(self::APRUVE_INVOICE_ID);

        $this->paymentTransaction->expects(self::once())
            ->method('setRequest')
            ->with(self::REQUEST_DATA)
            ->willReturnSelf();

        $this->mockPaymentTransactionResult($isSuccessful, $isSuccessful);

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(self::RETURN_SUCCESS, $actual);
    }

    public function testExecuteWhenResponseIsNotSuccessful()
    {
        $isSuccessful = false;

        $this->mockPaymentContextFactory($this->paymentContext);
        $this->mockApruveInvoiceFactory();
        $this->mockSourcePaymentTransaction(PaymentMethodInterface::AUTHORIZE, self::APRUVE_ORDER_ID);
        $this->mockApruveInvoiceRequest(self::REQUEST_DATA, self::APRUVE_ORDER_ID);
        $this->mockApruveRestClient($this->mockRestResponse($isSuccessful, self::RESPONSE_DATA));

        $this->paymentTransaction->expects(self::once())
            ->method('setResponse')
            ->with(self::RESPONSE_DATA)
            ->willReturnSelf();

        $this->paymentTransaction->expects(self::once())
            ->method('setReference')
            ->with(self::APRUVE_INVOICE_ID);

        $this->paymentTransaction->expects(self::once())
            ->method('setRequest')
            ->with(self::REQUEST_DATA)
            ->willReturnSelf();

        $this->mockPaymentTransactionResult($isSuccessful, $isSuccessful);

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(self::RETURN_ERROR, $actual);
    }

    public function testExecuteWhenRestException()
    {
        $isSuccessful = false;

        $this->mockApruveInvoiceRequest(self::REQUEST_DATA, self::APRUVE_ORDER_ID);
        $this->mockApruveInvoiceFactory();
        $this->mockPaymentTransactionResult($isSuccessful, $isSuccessful);
        $this->mockSourcePaymentTransaction(PaymentMethodInterface::AUTHORIZE, self::APRUVE_ORDER_ID);

        $this->prepareExecuteWhenRestException();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(self::RETURN_ERROR, $actual);
    }

    /**
     * {@inheritDoc}
     */
    protected function mockApruveRestClient(
        RestResponseInterface|\PHPUnit\Framework\MockObject\MockObject $restResponse
    ): void {
        parent::mockApruveRestClient($restResponse);

        $this->apruveInvoiceFromResponseFactory->expects(self::once())
            ->method('createFromResponse')
            ->with($restResponse)
            ->willReturn($this->mockCreatedApruveInvoice(self::APRUVE_INVOICE_ID));
    }

    private function mockApruveInvoiceRequest(array $requestData, string $apruveOrderId): void
    {
        $apruveInvoiceRequest = $this->createMock(ApruveRequestInterface::class);

        $apruveInvoiceRequest->expects(self::once())
            ->method('toArray')
            ->willReturn($requestData);

        $this->createInvoiceRequestFactory->expects(self::once())
            ->method('create')
            ->with($this->apruveInvoice, $apruveOrderId)
            ->willReturn($apruveInvoiceRequest);
    }

    private function mockCreatedApruveInvoice(string $apruveInvoiceId): ApruveInvoice
    {
        $apruveInvoice = $this->createMock(ApruveInvoice::class);
        $apruveInvoice->expects(self::once())
            ->method('getId')
            ->willReturn($apruveInvoiceId);

        return $apruveInvoice;
    }

    private function mockApruveInvoiceFactory()
    {
        $this->apruveEntityFromPaymentContextFactory->expects(self::once())
            ->method('createFromPaymentContext')
            ->with($this->paymentContext)
            ->willReturn($this->apruveInvoice);
    }
}
