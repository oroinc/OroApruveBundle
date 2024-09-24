<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\PaymentAction;

use Oro\Bundle\ApruveBundle\Apruve\Factory\Shipment\ApruveShipmentFromPaymentContextFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\Shipment\ApruveShipmentFromResponseFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveShipment;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\ApruveBundle\Client\Request\Shipment\Factory\CreateShipmentRequestFactoryInterface;
use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\ShipmentPaymentAction;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class ShipmentPaymentActionTest extends AbstractPaymentActionTest
{
    private const APRUVE_INVOICE_ID = 'sampleApruveOrderId';
    private const APRUVE_SHIPMENT_ID = 'sampleApruveShipmentId';

    protected const RETURN_ERROR = [
        'successful' => false,
        'message' => 'oro.apruve.payment_transaction.shipment.result.error'
    ];

    /** @var ApruveShipmentFromResponseFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveShipmentFromResponseFactory;

    /** @var CreateShipmentRequestFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $createShipmentRequestFactory;

    /** @var ApruveShipment|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveShipment;

    /** @var ApruveShipmentFromPaymentContextFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $apruveEntityFromPaymentContextFactory;

    /** @var ShipmentPaymentAction */
    protected $paymentAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentActionName = ShipmentPaymentAction::NAME;

        $this->apruveShipment = $this->createMock(ApruveShipment::class);
        $this->apruveEntityFromPaymentContextFactory
            = $this->createMock(ApruveShipmentFromPaymentContextFactoryInterface::class);

        $this->apruveShipmentFromResponseFactory
            = $this->createMock(ApruveShipmentFromResponseFactoryInterface::class);

        $this->createShipmentRequestFactory
            = $this->createMock(CreateShipmentRequestFactoryInterface::class);

        $this->paymentAction = new ShipmentPaymentAction(
            $this->paymentContextFactory,
            $this->apruveEntityFromPaymentContextFactory,
            $this->apruveShipmentFromResponseFactory,
            $this->apruveConfigRestClientFactory,
            $this->createShipmentRequestFactory
        );

        $this->setUpLoggerMock($this->paymentAction);
    }

    public function testExecute()
    {
        $isSuccessful = true;

        $this->mockPaymentContextFactory($this->paymentContext);
        $this->mockApruveShipmentFactory();
        $this->mockSourcePaymentTransaction(ApruvePaymentMethod::INVOICE, self::APRUVE_INVOICE_ID);
        $this->mockApruveShipmentRequest(self::REQUEST_DATA, self::APRUVE_INVOICE_ID);
        $this->mockApruveRestClient($this->mockRestResponse($isSuccessful, self::RESPONSE_DATA));

        $this->paymentTransaction->expects(self::once())
            ->method('setResponse')
            ->with(self::RESPONSE_DATA)
            ->willReturnSelf();

        $this->paymentTransaction->expects(self::once())
            ->method('setReference')
            ->with(self::APRUVE_SHIPMENT_ID);

        $this->paymentTransaction->expects(self::once())
            ->method('setRequest')
            ->with(self::REQUEST_DATA)
            ->willReturnSelf();

        $this->mockPaymentTransactionResult($isSuccessful, false);

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(self::RETURN_SUCCESS, $actual);
    }

    public function testExecuteWhenResponseIsNotSuccessful()
    {
        $isSuccessful = false;

        $this->mockPaymentContextFactory($this->paymentContext);
        $this->mockApruveShipmentFactory();
        $this->mockSourcePaymentTransaction(ApruvePaymentMethod::INVOICE, self::APRUVE_INVOICE_ID);
        $this->mockApruveShipmentRequest(self::REQUEST_DATA, self::APRUVE_INVOICE_ID);
        $this->mockApruveRestClient($this->mockRestResponse($isSuccessful, self::RESPONSE_DATA));

        $this->paymentTransaction->expects(self::once())
            ->method('setResponse')
            ->with(self::RESPONSE_DATA)
            ->willReturnSelf();

        $this->paymentTransaction->expects(self::once())
            ->method('setReference')
            ->with(self::APRUVE_SHIPMENT_ID);

        $this->paymentTransaction->expects(self::once())
            ->method('setRequest')
            ->with(self::REQUEST_DATA)
            ->willReturnSelf();

        $this->mockPaymentTransactionResult($isSuccessful, false);

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(self::RETURN_ERROR, $actual);
    }

    public function testExecuteWhenRestException()
    {
        $isSuccessful = false;

        $this->mockApruveShipmentRequest(self::REQUEST_DATA, self::APRUVE_INVOICE_ID);
        $this->mockApruveShipmentFactory();
        $this->mockPaymentTransactionResult($isSuccessful, false);
        $this->mockSourcePaymentTransaction(ApruvePaymentMethod::INVOICE, self::APRUVE_INVOICE_ID);
        $this->prepareExecuteWhenRestException();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(self::RETURN_ERROR, $actual);
    }

    protected function mockApruveRestClient(
        RestResponseInterface|\PHPUnit\Framework\MockObject\MockObject $restResponse
    ): void {
        parent::mockApruveRestClient($restResponse);

        $this->apruveShipmentFromResponseFactory->expects(self::once())
            ->method('createFromResponse')
            ->with($restResponse)
            ->willReturn($this->mockCreatedApruveShipment(self::APRUVE_SHIPMENT_ID));
    }

    private function mockApruveShipmentRequest(array $requestData, string $apruveInvoiceId): void
    {
        $apruveShipmentRequest = $this->createMock(ApruveRequestInterface::class);
        $apruveShipmentRequest->expects(self::once())
            ->method('toArray')
            ->willReturn($requestData);

        $this->createShipmentRequestFactory->expects(self::once())
            ->method('create')
            ->with($this->apruveShipment, $apruveInvoiceId)
            ->willReturn($apruveShipmentRequest);
    }

    private function mockCreatedApruveShipment(string $apruveShipmentId): ApruveShipment
    {
        $apruveShipment = $this->createMock(ApruveShipment::class);
        $apruveShipment->expects(self::once())
            ->method('getId')
            ->willReturn($apruveShipmentId);

        return $apruveShipment;
    }

    private function mockApruveShipmentFactory()
    {
        $this->apruveEntityFromPaymentContextFactory->expects(self::once())
            ->method('createFromPaymentContext')
            ->with($this->paymentContext)
            ->willReturn($this->apruveShipment);
    }
}
