<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\PaymentAction\Executor;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\Executor\PaymentActionExecutor;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\PaymentActionInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

class PaymentActionExecutorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PaymentActionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentAction;

    /**
     * @var PaymentTransaction|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentTransaction;

    /**
     * @var PaymentActionExecutor
     */
    private $paymentActionExecutor;

    /**
     * @var ApruveConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $config;


    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->paymentActionExecutor = new PaymentActionExecutor();
        $this->paymentTransaction = $this->createMock(PaymentTransaction::class);
        $this->config = $this->createMock(ApruveConfigInterface::class);

        $this->paymentAction = $this->createMock(PaymentActionInterface::class);
        $this->paymentAction
            ->expects($this->once())
            ->method('getName')
            ->willReturn('supported_action');

        $this->paymentActionExecutor->addPaymentAction($this->paymentAction);
    }

    public function testAddPaymentAction()
    {
        /** @var PaymentActionInterface|\PHPUnit\Framework\MockObject\MockObject $paymentAction */
        $paymentAction = $this->createMock(PaymentActionInterface::class);
        $paymentAction
            ->expects($this->once())
            ->method('getName')
            ->willReturn('purchase');

        $return = $this->paymentActionExecutor->addPaymentAction($paymentAction);
        static::assertSame($return, $this->paymentActionExecutor);

        $actionsProp = new \ReflectionProperty(PaymentActionExecutor::class, 'actions');
        $actionsProp->setAccessible(true);
        $actions = $actionsProp->getValue($this->paymentActionExecutor);

        static::assertArraySubset(['purchase' => $paymentAction], $actions);
    }

    public function testExecuteWithSupportedAction()
    {
        $this->paymentAction
            ->expects($this->once())
            ->method('execute')
            ->with($this->config, $this->paymentTransaction)
            ->willReturn([]);

        $actual = $this->paymentActionExecutor->execute('supported_action', $this->config, $this->paymentTransaction);

        static::assertSame([], $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Payment action with name "unsupported_action" is not supported
     */
    public function testExecuteWithUnsupportedAction()
    {
        $actual = $this->paymentActionExecutor->execute('unsupported_action', $this->config, $this->paymentTransaction);

        static::assertSame([], $actual);
    }

    /**
     * @dataProvider supportsDataProvider
     *
     * @param string $actionName
     * @param bool   $expected
     */
    public function testSupports($actionName, $expected)
    {
        $actual = $this->paymentActionExecutor->supports($actionName);
        static::assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function supportsDataProvider()
    {
        return [
            ['supported_action', true],
            ['unsupported_action', false],
        ];
    }
}
