<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\PaymentAction\Executor;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\Executor\PaymentActionExecutor;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\PaymentActionInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Component\Testing\ReflectionUtil;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaymentActionExecutorTest extends TestCase
{
    private PaymentTransaction&MockObject $paymentTransaction;
    private ApruveConfigInterface&MockObject $config;
    private PaymentActionInterface&MockObject $paymentAction;
    private PaymentActionExecutor $paymentActionExecutor;

    #[\Override]
    protected function setUp(): void
    {
        $this->paymentTransaction = $this->createMock(PaymentTransaction::class);
        $this->config = $this->createMock(ApruveConfigInterface::class);

        $this->paymentAction = $this->createMock(PaymentActionInterface::class);
        $this->paymentAction->expects(self::once())
            ->method('getName')
            ->willReturn('supported_action');

        $this->paymentActionExecutor = new PaymentActionExecutor();
        $this->paymentActionExecutor->addPaymentAction($this->paymentAction);
    }

    public function testAddPaymentAction(): void
    {
        $paymentAction = $this->createMock(PaymentActionInterface::class);
        $paymentAction->expects(self::once())
            ->method('getName')
            ->willReturn('purchase');

        $return = $this->paymentActionExecutor->addPaymentAction($paymentAction);
        self::assertSame($return, $this->paymentActionExecutor);

        $actions = ReflectionUtil::getPropertyValue($this->paymentActionExecutor, 'actions');
        $this->assertSame($paymentAction, $actions['purchase']);
    }

    public function testExecuteWithSupportedAction(): void
    {
        $this->paymentAction->expects(self::once())
            ->method('execute')
            ->with($this->config, $this->paymentTransaction)
            ->willReturn([]);

        $actual = $this->paymentActionExecutor->execute('supported_action', $this->config, $this->paymentTransaction);

        self::assertSame([], $actual);
    }

    public function testExecuteWithUnsupportedAction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment action with name "unsupported_action" is not supported');

        $actual = $this->paymentActionExecutor->execute('unsupported_action', $this->config, $this->paymentTransaction);

        self::assertSame([], $actual);
    }

    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(string $actionName, bool $expected): void
    {
        $actual = $this->paymentActionExecutor->supports($actionName);
        self::assertSame($expected, $actual);
    }

    public function supportsDataProvider(): array
    {
        return [
            ['supported_action', true],
            ['unsupported_action', false],
        ];
    }
}
