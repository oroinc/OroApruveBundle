<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\EventListener\Callback;

use Oro\Bundle\ApruveBundle\EventListener\Callback\PaymentCallbackListener;
use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Event\CallbackNotifyEvent;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Oro\Bundle\PaymentBundle\Method\Provider\PaymentMethodProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class PaymentCallbackListenerTest extends \PHPUnit\Framework\TestCase
{
    private const EVENT_DATA = [ApruvePaymentMethod::PARAM_ORDER_ID => 'sampleApuveOrderId'];

    /** @var PaymentCallbackListener */
    private $listener;

    /** @var PaymentMethodProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentMethodProvider;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    protected function setUp(): void
    {
        $this->paymentMethodProvider = $this->createMock(PaymentMethodProviderInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->listener = new PaymentCallbackListener($this->paymentMethodProvider);
        $this->listener->setLogger($this->logger);
    }

    public function testOnReturn()
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method');

        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->paymentMethodProvider->expects(self::once())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $this->paymentMethodProvider->expects(self::once())
            ->method('getPaymentMethod')
            ->with('payment_method')
            ->willReturn($paymentMethod);

        $paymentMethod->expects(self::once())
            ->method('execute')
            ->with('authorize', $paymentTransaction);

        $event = new CallbackNotifyEvent(self::EVENT_DATA);
        $event->setPaymentTransaction($paymentTransaction);

        self::assertEquals(Response::HTTP_FORBIDDEN, $event->getResponse()->getStatusCode());

        $this->listener->onReturn($event);

        self::assertEquals('action', $paymentTransaction->getAction());
        self::assertEquals(Response::HTTP_OK, $event->getResponse()->getStatusCode());
        self::assertEquals(self::EVENT_DATA, $paymentTransaction->getResponse());
    }

    public function testOnReturnWithoutTransaction()
    {
        $event = new CallbackNotifyEvent(self::EVENT_DATA);

        self::assertEquals(Response::HTTP_FORBIDDEN, $event->getResponse()->getStatusCode());
        $this->listener->onReturn($event);
        self::assertEquals(Response::HTTP_FORBIDDEN, $event->getResponse()->getStatusCode());
    }

    public function testOnReturnWithInvalidPaymentMethod()
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setPaymentMethod('payment_method');

        $this->paymentMethodProvider->expects(self::any())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(false);

        $this->paymentMethodProvider->expects(self::never())
            ->method('getPaymentMethod');

        $event = new CallbackNotifyEvent(self::EVENT_DATA);
        $event->setPaymentTransaction($paymentTransaction);

        $this->listener->onReturn($event);
    }

    public function testOnReturnWithoutOrderId()
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method')
            ->setResponse(['existing' => 'response']);

        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $this->paymentMethodProvider->expects(self::any())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $this->paymentMethodProvider->expects(self::any())
            ->method('getPaymentMethod')
            ->with('payment_method')
            ->willReturn($paymentMethod);

        $paymentMethod->expects(self::never())
            ->method('execute');

        $event = new CallbackNotifyEvent([]);
        $event->setPaymentTransaction($paymentTransaction);

        $this->listener->onReturn($event);
    }

    public function testOnReturnExecuteFailed()
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method')
            ->setResponse(['existing' => 'response']);

        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $this->paymentMethodProvider->expects(self::any())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $this->paymentMethodProvider->expects(self::any())
            ->method('getPaymentMethod')
            ->with('payment_method')
            ->willReturn($paymentMethod);

        $paymentMethod->expects(self::once())
            ->method('execute')
            ->willThrowException(new \InvalidArgumentException());

        $event = new CallbackNotifyEvent(self::EVENT_DATA);
        $event->setPaymentTransaction($paymentTransaction);

        $this->logger->expects(self::once())
            ->method('error')
            ->with(
                self::isType('string'),
                self::logicalAnd(self::isType('array'), self::isEmpty())
            );

        $this->listener->onReturn($event);

        self::assertEquals(Response::HTTP_FORBIDDEN, $event->getResponse()->getStatusCode());
    }
}
