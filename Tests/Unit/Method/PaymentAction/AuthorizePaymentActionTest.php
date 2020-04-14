<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\PaymentAction;

use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\AuthorizePaymentAction;
use Oro\Bundle\PaymentBundle\Context\Factory\TransactionPaymentContextFactoryInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

class AuthorizePaymentActionTest extends \PHPUnit\Framework\TestCase
{
    const APRUVE_ORDER_ID = 'sampleApruveOrderId';
    const RESPONSE = [ApruvePaymentMethod::PARAM_ORDER_ID => self::APRUVE_ORDER_ID];

    /**
     * @var AuthorizePaymentAction
     */
    private $paymentAction;

    /**
     * @var TransactionPaymentContextFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentContextFactory;

    /**
     * @var PaymentTransaction|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentTransaction;

    /**
     * @var ApruveConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $config;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->paymentContextFactory = $this->createMock(TransactionPaymentContextFactoryInterface::class);
        $this->paymentTransaction = $this->createMock(PaymentTransaction::class);
        $this->config = $this->createMock(ApruveConfigInterface::class);

        $this->paymentAction = new AuthorizePaymentAction($this->paymentContextFactory);
    }

    public function testExecute()
    {
        $this->paymentTransaction
            ->expects(static::once())
            ->method('getResponse')
            ->willReturn(self::RESPONSE);

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setReference')
            ->with(self::APRUVE_ORDER_ID);

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setAction')
            ->with('authorize');

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setSuccessful')
            ->with(true);

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setActive')
            ->with(true);

        $actual = $this->paymentAction->execute($this->config, $this->paymentTransaction);

        static::assertSame([], $actual);
    }

    public function testGetName()
    {
        $actual = $this->paymentAction->getName();

        static::assertSame(AuthorizePaymentAction::NAME, $actual);
    }
}
