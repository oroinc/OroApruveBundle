<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\PaymentAction;

use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\AuthorizePaymentAction;
use Oro\Bundle\PaymentBundle\Context\Factory\TransactionPaymentContextFactoryInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthorizePaymentActionTest extends TestCase
{
    private const APRUVE_ORDER_ID = 'sampleApruveOrderId';
    private const RESPONSE = [ApruvePaymentMethod::PARAM_ORDER_ID => self::APRUVE_ORDER_ID];

    private TransactionPaymentContextFactoryInterface&MockObject $paymentContextFactory;
    private PaymentTransaction&MockObject $paymentTransaction;
    private ApruveConfigInterface&MockObject $config;
    private AuthorizePaymentAction $paymentAction;

    #[\Override]
    protected function setUp(): void
    {
        $this->paymentContextFactory = $this->createMock(TransactionPaymentContextFactoryInterface::class);
        $this->paymentTransaction = $this->createMock(PaymentTransaction::class);
        $this->config = $this->createMock(ApruveConfigInterface::class);

        $this->paymentAction = new AuthorizePaymentAction($this->paymentContextFactory);
    }

    public function testExecute(): void
    {
        $this->paymentTransaction->expects(self::once())
            ->method('getResponse')
            ->willReturn(self::RESPONSE);

        $this->paymentTransaction->expects(self::once())
            ->method('setReference')
            ->with(self::APRUVE_ORDER_ID);

        $this->paymentTransaction->expects(self::once())
            ->method('setAction')
            ->with('authorize');

        $this->paymentTransaction->expects(self::once())
            ->method('setSuccessful')
            ->with(true);

        $this->paymentTransaction->expects(self::once())
            ->method('setActive')
            ->with(true);

        $actual = $this->paymentAction->execute($this->config, $this->paymentTransaction);

        self::assertSame([], $actual);
    }

    public function testGetName(): void
    {
        $actual = $this->paymentAction->getName();

        self::assertSame(AuthorizePaymentAction::NAME, $actual);
    }
}
