<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method;

use Oro\Bundle\ApruveBundle\Apruve\Provider\SupportedCurrenciesProviderInterface;
use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\Executor\PaymentActionExecutor;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

class ApruvePaymentMethodTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApruvePaymentMethod */
    private $method;

    /** @var SupportedCurrenciesProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $supportedCurrenciesProvider;

    /** @var PaymentActionExecutor|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentActionExecutor;

    /** @var ApruveConfigInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(ApruveConfigInterface::class);
        $this->supportedCurrenciesProvider = $this->createMock(SupportedCurrenciesProviderInterface::class);
        $this->paymentActionExecutor = $this->createMock(PaymentActionExecutor::class);

        $this->method = new ApruvePaymentMethod(
            $this->config,
            $this->supportedCurrenciesProvider,
            $this->paymentActionExecutor
        );
    }

    public function testExecute()
    {
        $paymentTransaction = $this->createMock(PaymentTransaction::class);
        $action = 'some_action';

        $this->paymentActionExecutor->expects(self::once())
            ->method('execute')
            ->with($action, $this->config, $paymentTransaction)
            ->willReturn([]);

        $actual = $this->method->execute($action, $paymentTransaction);

        self::assertSame([], $actual);
    }

    public function testGetIdentifier()
    {
        $identifier = 'id';

        $this->config->expects(self::once())
            ->method('getPaymentMethodIdentifier')
            ->willReturn($identifier);

        self::assertEquals($identifier, $this->method->getIdentifier());
    }

    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(bool $expected, string $actionName)
    {
        $this->paymentActionExecutor->expects(self::once())
            ->method('supports')
            ->willReturnMap([
                [ApruvePaymentMethod::AUTHORIZE, true],
                [ApruvePaymentMethod::CAPTURE, true],
                [ApruvePaymentMethod::VALIDATE, false],
                [ApruvePaymentMethod::PURCHASE, false],
                [ApruvePaymentMethod::CHARGE, false],
            ]);

        self::assertEquals($expected, $this->method->supports($actionName));
    }

    public function supportsDataProvider(): array
    {
        return [
            [true, ApruvePaymentMethod::AUTHORIZE],
            [true, ApruvePaymentMethod::CAPTURE],
            [false, ApruvePaymentMethod::VALIDATE],
            [false, ApruvePaymentMethod::PURCHASE],
            [false, ApruvePaymentMethod::CHARGE],
        ];
    }

    /**
     * @dataProvider isApplicableDataProvider
     */
    public function testIsApplicable(string $currency, bool $isSupported, bool $expectedResult)
    {
        $context = $this->createMock(PaymentContextInterface::class);
        $context->expects(self::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $this->supportedCurrenciesProvider->expects(self::any())
            ->method('isSupported')
            ->with($currency)
            ->willReturn($isSupported);

        $actual = $this->method->isApplicable($context);
        self::assertSame($expectedResult, $actual);
    }

    public function isApplicableDataProvider(): array
    {
        return [
            'should be applicable if currency is supported' => ['USD', true, true],
            'should be inapplicable if currency is not supported' => ['EUR', false, false],
        ];
    }
}
