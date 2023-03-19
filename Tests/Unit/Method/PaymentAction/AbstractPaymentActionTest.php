<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\PaymentAction;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\Config\ApruveConfigRestClientFactoryInterface;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\PaymentActionInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Exception\RestException;
use Oro\Bundle\PaymentBundle\Context\Factory\TransactionPaymentContextFactoryInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\TestFrameworkBundle\Test\Logger\LoggerAwareTraitTestTrait;

abstract class AbstractPaymentActionTest extends \PHPUnit\Framework\TestCase
{
    use LoggerAwareTraitTestTrait;

    protected const RESPONSE_DATA = [
        'id' => 'sampleId',
    ];

    protected const REQUEST_DATA = [
        'method' => 'POST',
        'uri' => 'sampleUri/1',
        'data' => [
            'amount_cents' => 1000,
            'currency' => 'USD',
            'shippedAt' => '2027-04-15T10:12:27-05:00',
        ],
    ];

    protected const RETURN_SUCCESS = ['successful' => true];
    protected const RETURN_ERROR = ['successful' => false];

    /** @var string */
    protected $paymentActionName;

    /** @var ApruveRestClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $apruveRestClient;

    /** @var ApruveConfigRestClientFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $apruveConfigRestClientFactory;

    /** @var PaymentContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $paymentContext;

    /** @var TransactionPaymentContextFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $paymentContextFactory;

    /** @var PaymentTransaction|\PHPUnit\Framework\MockObject\MockObject */
    protected $paymentTransaction;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $apruveEntityFromPaymentContextFactory;

    /** @var PaymentActionInterface */
    protected $paymentAction;

    protected function setUp(): void
    {
        $this->paymentTransaction = $this->createMock(PaymentTransaction::class);

        $this->paymentContext = $this->createMock(PaymentContextInterface::class);
        $this->paymentContextFactory = $this->createMock(TransactionPaymentContextFactoryInterface::class);

        $this->apruveRestClient = $this->createMock(ApruveRestClientInterface::class);
        $this->apruveConfigRestClientFactory
            = $this->createMock(ApruveConfigRestClientFactoryInterface::class);
    }

    public function testGetName()
    {
        $actual = $this->paymentAction->getName();

        self::assertSame($this->paymentActionName, $actual);
    }

    public function testExecuteWithoutPaymentContext()
    {
        $this->mockPaymentContextFactory();

        $this->apruveEntityFromPaymentContextFactory->expects(self::never())
            ->method('createFromPaymentContext');

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(static::RETURN_ERROR, $actual);
    }

    public function testExecuteWithoutSourcePaymentTransaction()
    {
        $transactionId = 1234;

        $this->mockPaymentContextFactory($this->paymentContext);

        $this->paymentTransaction->expects(self::once())
            ->method('getId')
            ->willReturn($transactionId);

        $this->paymentTransaction->expects(self::once())
            ->method('getSourcePaymentTransaction')
            ->willReturn(null);

        $this->apruveEntityFromPaymentContextFactory->expects(self::never())
            ->method('createFromPaymentContext');

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(static::RETURN_ERROR, $actual);
    }

    public function testExecuteWhenInvalidSourcePaymentTransaction()
    {
        $transactionId = 1234;

        $this->mockPaymentContextFactory($this->paymentContext);

        $this->paymentTransaction->expects(self::once())
            ->method('getId')
            ->willReturn($transactionId);

        $this->mockSourcePaymentTransaction('invalid_action', null);

        $this->apruveEntityFromPaymentContextFactory->expects(self::never())
            ->method('createFromPaymentContext');

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->paymentAction->execute($this->mockApruveConfig(), $this->paymentTransaction);

        self::assertSame(static::RETURN_ERROR, $actual);
    }

    protected function prepareExecuteWhenRestException()
    {
        $this->mockPaymentContextFactory($this->paymentContext);

        $this->apruveConfigRestClientFactory->expects(self::once())
            ->method('create')
            ->with($this->mockApruveConfig())
            ->willReturn($this->apruveRestClient);

        $this->apruveRestClient->expects(self::once())
            ->method('execute')
            ->willThrowException($this->createRestException());

        $this->paymentTransaction->expects(self::never())
            ->method('setResponse');

        $this->paymentTransaction->expects(self::once())
            ->method('setRequest')
            ->with(static::REQUEST_DATA)
            ->willReturnSelf();

        $this->assertLoggerErrorMethodCalled();
    }

    protected function mockSourcePaymentTransaction(string $action, ?string $reference): void
    {
        $sourcePaymentTransaction = $this->createMock(PaymentTransaction::class);
        $sourcePaymentTransaction->expects(self::once())
            ->method('getAction')
            ->willReturn($action);
        $sourcePaymentTransaction->expects(self::any())
            ->method('getReference')
            ->willReturn($reference);

        $this->paymentTransaction->expects(self::once())
            ->method('getSourcePaymentTransaction')
            ->willReturn($sourcePaymentTransaction);
    }

    protected function mockApruveRestClient(
        RestResponseInterface|\PHPUnit\Framework\MockObject\MockObject $restResponse
    ): void {
        $this->apruveConfigRestClientFactory->expects(self::once())
            ->method('create')
            ->with($this->mockApruveConfig())
            ->willReturn($this->apruveRestClient);

        $this->apruveRestClient->expects(self::once())
            ->method('execute')
            ->willReturn($restResponse);
    }

    protected function mockRestResponse(bool $isSuccessful, array $responseData): RestResponseInterface
    {
        $restResponse = $this->createMock(RestResponseInterface::class);
        $restResponse->expects(self::once())
            ->method('isSuccessful')
            ->willReturn($isSuccessful);
        $restResponse->expects(self::once())
            ->method('json')
            ->willReturn($responseData);

        return $restResponse;
    }

    protected function mockApruveConfig(): ApruveConfigInterface
    {
        return $this->createMock(ApruveConfigInterface::class);
    }

    protected function mockPaymentTransactionResult(bool $isSuccessful, bool $isActive): void
    {
        $this->paymentTransaction->expects(self::once())
            ->method('setSuccessful')
            ->with($isSuccessful)
            ->willReturnSelf();
        $this->paymentTransaction->expects(self::once())
            ->method('setActive')
            ->with($isActive);
    }

    protected function createRestException(): RestException
    {
        return new RestException();
    }

    protected function mockPaymentContextFactory(PaymentContextInterface $paymentContext = null)
    {
        $this->paymentContextFactory->expects(self::once())
            ->method('create')
            ->with($this->paymentTransaction)
            ->willReturn($paymentContext);
    }
}
