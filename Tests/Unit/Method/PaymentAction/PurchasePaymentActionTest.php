<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\PaymentAction;

use Oro\Bundle\ApruveBundle\Apruve\Factory\Order\ApruveOrderFromPaymentContextFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Generator\OrderSecureHashGeneratorInterface;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveOrder;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\PurchasePaymentAction;
use Oro\Bundle\PaymentBundle\Context\Factory\TransactionPaymentContextFactoryInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\TestFrameworkBundle\Test\Logger\LoggerAwareTraitTestTrait;

class PurchasePaymentActionTest extends \PHPUnit\Framework\TestCase
{
    use LoggerAwareTraitTestTrait;

    const API_KEY = 'sampleApiKey';
    const APRUVE_ORDER = [
        ApruveOrder::MERCHANT_ID => 'sampleId',
        ApruveOrder::AMOUNT_CENTS => 10000,
        ApruveOrder::CURRENCY => 'USD',
        ApruveOrder::SHIPPING_CENTS => '500',
        ApruveOrder::FINALIZE_ON_CREATE => true,
        ApruveOrder::INVOICE_ON_CREATE => false,
        ApruveOrder::LINE_ITEMS => [
            [
                ApruveLineItem::TITLE => 'Sample title',
                ApruveLineItem::AMOUNT_CENTS => 10000,
                ApruveLineItem::QUANTITY => 10,
                ApruveLineItem::DESCRIPTION => "Sample" . PHP_EOL . "description with line break",
                ApruveLineItem::SKU => 'sku1',
                ApruveLineItem::VIEW_PRODUCT_URL => 'http://example.com/product/view/1',
            ],
        ],
        ApruveOrder::FINALIZE_ON_CREATE => true,
        ApruveOrder::INVOICE_ON_CREATE => false,
    ];
    const SECURE_HASH = '6c6b4a10f9afc452a065051ff42da575264d937f77888b4397fd85d0c12d2109';

    const REQUEST_DATA = [
        'apruveOrder' => self::APRUVE_ORDER,
        'apruveOrderSecureHash' => self::SECURE_HASH,
    ];

    /**
     * @var ApruveOrder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apruveOrder;

    /**
     * @var PaymentContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentContext;

    /**
     * @var ApruveOrderFromPaymentContextFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apruveOrderFromPaymentContextFactory;

    /**
     * @var OrderSecureHashGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderSecureHashGenerator;

    /**
     * @var PurchasePaymentAction
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
        $this->paymentTransaction = $this->createMock(PaymentTransaction::class);
        $this->paymentContext = $this->createMock(PaymentContextInterface::class);
        $this->paymentContextFactory = $this->createMock(TransactionPaymentContextFactoryInterface::class);
        $this->apruveOrder = $this->createMock(ApruveOrder::class);
        $this->apruveOrderFromPaymentContextFactory
            = $this->createMock(ApruveOrderFromPaymentContextFactoryInterface::class);
        $this->orderSecureHashGenerator = $this->createMock(OrderSecureHashGeneratorInterface::class);
        $this->config = $this->createMock(ApruveConfigInterface::class);

        $this->paymentAction = new PurchasePaymentAction(
            $this->paymentContextFactory,
            $this->apruveOrderFromPaymentContextFactory,
            $this->orderSecureHashGenerator
        );

        $this->setUpLoggerMock($this->paymentAction);
    }

    public function testExecute()
    {
        $this->paymentContextFactory
            ->expects(static::once())
            ->method('create')
            ->with($this->paymentTransaction)
            ->willReturn($this->paymentContext);

        $this->apruveOrder
            ->expects(static::once())
            ->method('getData')
            ->willReturn(self::APRUVE_ORDER);

        $this->config
            ->expects(static::once())
            ->method('getApiKey')
            ->willReturn(self::API_KEY);

        $this->apruveOrderFromPaymentContextFactory
            ->expects(static::once())
            ->method('createFromPaymentContext')
            ->with($this->paymentContext, $this->config)
            ->willReturn($this->apruveOrder);

        $this->orderSecureHashGenerator
            ->expects(static::once())
            ->method('generate')
            ->with($this->apruveOrder, self::API_KEY)
            ->willReturn(self::SECURE_HASH);

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setRequest')
            ->with(self::REQUEST_DATA);

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setSuccessful')
            ->with(false);

        $this->paymentTransaction
            ->expects(static::once())
            ->method('setActive')
            ->with(true);

        $actual = $this->paymentAction->execute($this->config, $this->paymentTransaction);

        static::assertSame(self::REQUEST_DATA, $actual);
    }

    public function testExecuteWithoutPaymentContext()
    {
        $this->paymentContextFactory
            ->expects(static::once())
            ->method('create')
            ->with($this->paymentTransaction)
            ->willReturn(null);

        $this->apruveOrderFromPaymentContextFactory
            ->expects(static::never())
            ->method('createFromPaymentContext');

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->paymentAction->execute($this->config, $this->paymentTransaction);

        static::assertSame([], $actual);
    }

    public function testGetName()
    {
        $actual = $this->paymentAction->getName();

        static::assertSame(PurchasePaymentAction::NAME, $actual);
    }
}
