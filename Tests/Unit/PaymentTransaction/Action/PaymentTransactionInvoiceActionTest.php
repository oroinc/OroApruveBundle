<?php

namespace Oro\Bundle\PaymentBundle\Tests\Unit\Action;

use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\PaymentTransaction\Action\PaymentTransactionInvoiceAction;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Oro\Bundle\PaymentBundle\Method\Provider\PaymentMethodProviderInterface;
use Oro\Bundle\PaymentBundle\Provider\PaymentTransactionProvider;
use Oro\Component\ConfigExpression\ContextAccessor;
use Oro\Component\Testing\ReflectionUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\RouterInterface;

class PaymentTransactionInvoiceActionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ContextAccessor|\PHPUnit\Framework\MockObject\MockObject */
    private $contextAccessor;

    /** @var PaymentMethodProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentMethodProvider;

    /** @var PaymentTransactionProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentTransactionProvider;

    /** @var RouterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $router;

    /** @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var PaymentTransactionInvoiceAction */
    private $action;

    protected function setUp(): void
    {
        $this->contextAccessor = $this->createMock(ContextAccessor::class);
        $this->paymentMethodProvider = $this->createMock(PaymentMethodProviderInterface::class);
        $this->paymentTransactionProvider = $this->createMock(PaymentTransactionProvider::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->action = new PaymentTransactionInvoiceAction(
            $this->contextAccessor,
            $this->paymentMethodProvider,
            $this->paymentTransactionProvider,
            $this->router
        );
        $this->action->setLogger($this->logger);
        $this->action->setDispatcher($this->dispatcher);
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $data, array $expected)
    {
        /** @var PaymentTransaction $authorizationPaymentTransaction */
        $authorizationPaymentTransaction = $data['options']['paymentTransaction'];
        $invoicePaymentTransaction = $data['invoicePaymentTransaction'];
        $shipmentPaymentTransaction = $data['shipmentPaymentTransaction'];
        $options = $data['options'];
        $context = [];

        $this->contextAccessor->expects(self::any())
            ->method('getValue')
            ->willReturnArgument(1);

        $this->paymentTransactionProvider->expects(self::atLeastOnce())
            ->method('createPaymentTransactionByParentTransaction')
            ->willReturnMap([
                [ApruvePaymentMethod::INVOICE, $authorizationPaymentTransaction, $invoicePaymentTransaction],
                [ApruvePaymentMethod::SHIPMENT, $invoicePaymentTransaction, $shipmentPaymentTransaction],
            ]);

        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $paymentMethod->expects(self::atLeastOnce())
            ->method('execute')
            ->willReturnMap([
                [ApruvePaymentMethod::INVOICE, $invoicePaymentTransaction, $data['invoiceResponse']],
                [ApruvePaymentMethod::SHIPMENT, $shipmentPaymentTransaction, $data['shipmentResponse']],
            ]);

        $this->paymentMethodProvider->expects(self::atLeastOnce())
            ->method('hasPaymentMethod')
            ->with($authorizationPaymentTransaction->getPaymentMethod())
            ->willReturn(true);

        $this->paymentMethodProvider->expects(self::atLeastOnce())
            ->method('getPaymentMethod')
            ->with($authorizationPaymentTransaction->getPaymentMethod())
            ->willReturn($paymentMethod);

        $this->paymentTransactionProvider->expects(self::atLeastOnce())
            ->method('savePaymentTransaction')
            ->withConsecutive(
                [$invoicePaymentTransaction],
                [$shipmentPaymentTransaction]
            );

        $this->contextAccessor->expects(self::once())
            ->method('setValue')
            ->with($context, $options['attribute'], $expected);

        $this->action->initialize($options);
        $this->action->execute($context);
    }

    public function executeDataProvider(): array
    {
        return [
            'successful' => [
                'data' => [
                    'invoicePaymentTransaction' => $this->getPaymentTransaction(10)
                        ->setAction(ApruvePaymentMethod::INVOICE)
                        ->setEntityIdentifier(10)
                        ->setSuccessful(true),
                    'shipmentPaymentTransaction' => $this->getPaymentTransaction(20)
                        ->setAction(ApruvePaymentMethod::SHIPMENT)
                        ->setEntityIdentifier(20)
                        ->setSuccessful(true),
                    'options' => [
                        'paymentTransaction' => $this->getPaymentTransaction(1),
                        'attribute' => new PropertyPath('test'),
                        'transactionOptions' => [
                            'testOption' => 'testOption',
                        ],
                    ],
                    'invoiceResponse' => ['testInvoiceResponse' => 'testResponse'],
                    'shipmentResponse' => ['testShipmentResponse' => 'testResponse'],
                ],
                'expected' => [
                    'transaction' => 20,
                    'invoiceTransaction' => 10,
                    'successful' => true,
                    'message' => null,
                    'testOption' => 'testOption',
                    'testInvoiceResponse' => 'testResponse',
                    'testShipmentResponse' => 'testResponse',
                ],
            ],
            'invoice is not successful' => [
                'data' => [
                    'invoicePaymentTransaction' => $this->getPaymentTransaction(10)
                                                        ->setAction(ApruvePaymentMethod::INVOICE)
                                                        ->setEntityIdentifier(10)
                                                        ->setSuccessful(false),
                    'shipmentPaymentTransaction' => null,
                    'options' => [
                        'paymentTransaction' => $this->getPaymentTransaction(1),
                        'attribute' => new PropertyPath('test'),
                        'transactionOptions' => [
                            'testOption' => 'testOption',
                        ],
                    ],
                    'invoiceResponse' => ['testInvoiceResponse' => 'testResponse'],
                    'shipmentResponse' => null,
                ],
                'expected' => [
                    'transaction' => 10,
                    'successful' => false,
                    'message' => null,
                    'testOption' => 'testOption',
                    'testInvoiceResponse' => 'testResponse',
                ],
            ],
            'shipment is not successful' => [
                'data' => [
                    'invoicePaymentTransaction' => $this->getPaymentTransaction(10)
                                                        ->setAction(ApruvePaymentMethod::INVOICE)
                                                        ->setEntityIdentifier(10)
                                                        ->setSuccessful(true),
                    'shipmentPaymentTransaction' => $this->getPaymentTransaction(20)
                                                         ->setAction(ApruvePaymentMethod::SHIPMENT)
                                                         ->setEntityIdentifier(20)
                                                         ->setSuccessful(false),
                    'options' => [
                        'paymentTransaction' => $this->getPaymentTransaction(1),
                        'attribute' => new PropertyPath('test'),
                        'transactionOptions' => [
                            'testOption' => 'testOption',
                        ],
                    ],
                    'invoiceResponse' => ['testInvoiceResponse' => 'testResponse'],
                    'shipmentResponse' => ['testShipmentResponse' => 'testResponse'],
                ],
                'expected' => [
                    'transaction' => 20,
                    'invoiceTransaction' => 10,
                    'successful' => false,
                    'message' => null,
                    'testOption' => 'testOption',
                    'testInvoiceResponse' => 'testResponse',
                    'testShipmentResponse' => 'testResponse',
                ],
            ],
        ];
    }

    /**
     * @dataProvider executeWrongOptionsDataProvider
     */
    public function testExecuteWrongOptions(array $options)
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->action->initialize($options);
        $this->action->execute([]);
    }

    public function executeWrongOptionsDataProvider(): array
    {
        return [
            [['someOption' => 'someValue']],
            [['object' => 'someValue']],
            [['amount' => 'someAmount']],
            [['currency' => 'someCurrency']],
            [['paymentMethod' => 'somePaymentMethod']],
        ];
    }

    private function getPaymentTransaction(int $id): PaymentTransaction
    {
        $paymentTransaction = new PaymentTransaction();
        ReflectionUtil::setId($paymentTransaction, $id);
        $paymentTransaction->setPaymentMethod('testPaymentMethodType');

        return $paymentTransaction;
    }
}
