<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilderInterface;
use Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment\ApruveShipmentBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment\ApruveShipmentBuilderInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\Shipment\ApruveShipmentFromPaymentContextFactory;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;
use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProviderInterface;
use Oro\Bundle\ApruveBundle\Provider\TaxAmountProviderInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItemInterface;
use Oro\Bundle\PricingBundle\SubtotalProcessor\Model\Subtotal;
use Oro\Bundle\PricingBundle\SubtotalProcessor\TotalProcessorProvider;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;

class ApruveShipmentFromPaymentContextFactoryTest extends \PHPUnit\Framework\TestCase
{
    const AMOUNT = '100.1';
    const TOTAL_AMOUNT_CENTS = 12250;
    const TOTAL_AMOUNT_USD = 122.50;
    const AMOUNT_CENTS = 11130;
    const SHIPPING_AMOUNT = 10.1;
    const SHIPPING_AMOUNT_CENTS = 1010;
    const TAX_AMOUNT = 1.1;
    const TAX_AMOUNT_CENTS = 110;
    const CURRENCY = 'USD';
    const ISSUE_ON_CREATE = true;
    const SHIPPING_METHOD = 'sampleShippingMethod';
    const SHIPPING_METHOD_LABEL = 'Sample shipping method label';
    const LINE_ITEMS = [
        'sku1' => [
            'sku' => 'sku1',
            'quantity' => 100,
            'currency' => 'USD',
            'amount_cents' => 2000,
        ],
        'sku2' => [
            'sku' => 'sku2',
            'quantity' => 50,
            'currency' => 'USD',
            'amount_cents' => 1000,
        ],
    ];

    /**
     * @var ShippingMethodProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $shippingMethodProvider;

    /**
     * @var ApruveShipmentBuilderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apruveShipmentBuilder;

    /**
     * @var ApruveLineItemFromPaymentLineItemFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apruveLineItemFromPaymentLineItemFactory;

    /**
     * @var ApruveShipmentBuilderFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apruveShipmentBuilderFactory;

    /**
     * @var PaymentContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentContext;

    /**
     * @var ShippingAmountProviderInterface
     */
    private $shippingAmountProvider;

    /**
     * @var TaxAmountProviderInterface
     */
    private $taxAmountProvider;

    /**
     * @var ApruveShipmentFromPaymentContextFactory
     */
    private $factory;

    /**
     * @var TotalProcessorProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $totalProcessorProvider;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->paymentContext = $this->createMock(PaymentContextInterface::class);

        $this->paymentContext
            ->expects(static::once())
            ->method('getCurrency')
            ->willReturn(self::CURRENCY);

        $this->paymentContext
            ->expects(static::once())
            ->method('getShippingMethod')
            ->willReturn(self::SHIPPING_METHOD);

        $lineItemOne = $this->createMock(PaymentLineItemInterface::class);
        $lineItemTwo = $this->createMock(PaymentLineItemInterface::class);

        $this->paymentContext
            ->expects(static::once())
            ->method('getLineItems')
            ->willReturn([$lineItemOne, $lineItemTwo]);

        $this->shippingAmountProvider = $this->createMock(ShippingAmountProviderInterface::class);
        $this->shippingAmountProvider
            ->expects(static::exactly(1))
            ->method('getShippingAmount')
            ->with($this->paymentContext)
            ->willReturn(self::SHIPPING_AMOUNT);

        $this->taxAmountProvider = $this->createMock(TaxAmountProviderInterface::class);
        $this->taxAmountProvider
            ->expects(static::exactly(1))
            ->method('getTaxAmount')
            ->with($this->paymentContext)
            ->willReturn(self::TAX_AMOUNT);

        $this->apruveShipmentBuilder = $this->createMock(ApruveShipmentBuilderInterface::class);
        $this->apruveShipmentBuilderFactory = $this->createMock(ApruveShipmentBuilderFactoryInterface::class);

        $this->apruveLineItemFromPaymentLineItemFactory = $this
            ->createMock(ApruveLineItemFromPaymentLineItemFactoryInterface::class);
        $this->apruveLineItemFromPaymentLineItemFactory
            ->expects(static::exactly(2))
            ->method('createFromPaymentLineItem')
            ->willReturnMap([
                [$lineItemOne, $this->mockApruveLineItem(self::LINE_ITEMS['sku1'])],
                [$lineItemTwo, $this->mockApruveLineItem(self::LINE_ITEMS['sku2'])],
            ]);

        $this->shippingMethodProvider = $this->createMock(ShippingMethodProviderInterface::class);

        $this->totalProcessorProvider = $this->getMockBuilder(TotalProcessorProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new ApruveShipmentFromPaymentContextFactory(
            $this->mockAmountNormalizer(),
            $this->apruveLineItemFromPaymentLineItemFactory,
            $this->shippingAmountProvider,
            $this->taxAmountProvider,
            $this->totalProcessorProvider,
            $this->apruveShipmentBuilderFactory,
            $this->shippingMethodProvider
        );
    }

    public function testGetResult()
    {
        $this->shippingMethodProvider
            ->expects(static::once())
            ->method('hasShippingMethod')
            ->with(self::SHIPPING_METHOD)
            ->willReturn(true);

        $this->shippingMethodProvider
            ->expects(static::once())
            ->method('getShippingMethod')
            ->with(self::SHIPPING_METHOD)
            ->willReturn($this->mockShippingMethod(self::SHIPPING_METHOD_LABEL));

        $this->mockApruveShipmentBuilder();

        $this->apruveShipmentBuilder
            ->expects(static::once())
            ->method('setShipper')
            ->with(self::SHIPPING_METHOD_LABEL);

        $someEntity = new \stdClass();
        $this->paymentContext
            ->expects($this->once())
            ->method('getSourceEntity')
            ->willReturn($someEntity);

        $total = new Subtotal();
        $total->setAmount(self::TOTAL_AMOUNT_USD);

        $this->totalProcessorProvider
            ->expects($this->once())
            ->method('getTotal')
            ->with($someEntity)
            ->willReturn($total);

        $this->factory->createFromPaymentContext($this->paymentContext);
    }

    public function testGetResultIfNoShippingMethod()
    {
        $this->shippingMethodProvider
            ->expects(static::once())
            ->method('hasShippingMethod')
            ->with(self::SHIPPING_METHOD)
            ->willReturn(false);

        $this->shippingMethodProvider
            ->expects(static::never())
            ->method('getShippingMethod');

        $someEntity = new \stdClass();
        $this->paymentContext
            ->expects($this->once())
            ->method('getSourceEntity')
            ->willReturn($someEntity);

        $total = new Subtotal();
        $total->setAmount(self::TOTAL_AMOUNT_USD);

        $this->totalProcessorProvider
            ->expects($this->once())
            ->method('getTotal')
            ->with($someEntity)
            ->willReturn($total);

        $this->mockApruveShipmentBuilder();

        $this->factory->createFromPaymentContext($this->paymentContext);
    }

    /**
     * @param array $apruveLineItemData
     *
     * @return ApruveLineItemBuilderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function mockApruveLineItem(array $apruveLineItemData)
    {
        $apruveLineItem = $this->createMock(ApruveLineItem::class);
        $apruveLineItem
            ->expects(static::once())
            ->method('getData')
            ->willReturn($apruveLineItemData);

        return $apruveLineItem;
    }


    /**
     * @return AmountNormalizerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockAmountNormalizer()
    {
        $amountNormalizer = $this->createMock(AmountNormalizerInterface::class);
        $amountNormalizer
            ->method('normalize')
            ->willReturnMap([
                [self::TOTAL_AMOUNT_USD, self::TOTAL_AMOUNT_CENTS],
                [self::SHIPPING_AMOUNT, self::SHIPPING_AMOUNT_CENTS],
                [self::TAX_AMOUNT, self::TAX_AMOUNT_CENTS],
            ]);
        return $amountNormalizer;
    }

    /**
     * @param string $shippingMethodIdentifier
     *
     * @return ShippingMethodInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockShippingMethod($shippingMethodIdentifier)
    {
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);

        $shippingMethod
            ->expects(static::once())
            ->method('getLabel')
            ->willReturn($shippingMethodIdentifier);

        return $shippingMethod;
    }

    private function mockApruveShipmentBuilder()
    {
        $this->apruveShipmentBuilderFactory
            ->expects(static::once())
            ->method('create')
            ->with(
                self::TOTAL_AMOUNT_CENTS,
                self::CURRENCY,
                static::isType('string')
            )
            ->willReturn($this->apruveShipmentBuilder);

        $this->apruveShipmentBuilder
            ->expects(static::once())
            ->method('setLineItems')
            ->with([self::LINE_ITEMS['sku1'], self::LINE_ITEMS['sku2']])
            ->willReturnSelf();

        $this->apruveShipmentBuilder
            ->expects(static::once())
            ->method('setShippingCents')
            ->with(self::SHIPPING_AMOUNT_CENTS)
            ->willReturnSelf();

        $this->apruveShipmentBuilder
            ->expects(static::once())
            ->method('setTaxCents')
            ->with(self::TAX_AMOUNT_CENTS)
            ->willReturnSelf();

        $this->apruveShipmentBuilder
            ->expects(static::once())
            ->method('getResult');
    }
}
