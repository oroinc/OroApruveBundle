<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Order;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Order\ApruveOrderBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Builder\Order\ApruveOrderBuilderInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\Order\ApruveOrderFromPaymentContextFactory;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProviderInterface;
use Oro\Bundle\ApruveBundle\Provider\TaxAmountProviderInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItem;
use Oro\Bundle\PricingBundle\SubtotalProcessor\Model\Subtotal;
use Oro\Bundle\PricingBundle\SubtotalProcessor\TotalProcessorProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApruveOrderFromPaymentContextFactoryTest extends TestCase
{
    private const MERCHANT_ID = 'sampleMerchantId';
    private const TOTAL_AMOUNT_CENTS = 12250;
    private const TOTAL_AMOUNT_USD = 122.50;
    private const SHIPPING_AMOUNT = 10.1;
    private const SHIPPING_AMOUNT_CENTS = 1010;
    private const TAX_AMOUNT = 1.1;
    private const TAX_AMOUNT_CENTS = 110;
    private const CURRENCY = 'USD';
    private const MERCHANT_ORDER_ID = '123';
    private const FINALIZE_ON_CREATE = true;
    private const INVOICE_ON_CREATE = false;
    private const LINE_ITEMS = [
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

    private ApruveOrderBuilderInterface|MockObject $apruveOrderBuilder;

    private ApruveOrderBuilderFactoryInterface|MockObject $apruveOrderBuilderFactory;

    private PaymentContextInterface|MockObject $paymentContext;

    private ApruveOrderFromPaymentContextFactory $factory;

    private TotalProcessorProvider|MockObject $totalProcessorProvider;

    protected function setUp(): void
    {
        $this->totalProcessorProvider = $this->createMock(TotalProcessorProvider::class);
        $this->paymentContext = $this->createMock(PaymentContextInterface::class);
        $this->apruveOrderBuilder = $this->createMock(ApruveOrderBuilderInterface::class);
        $this->apruveOrderBuilderFactory = $this->createMock(ApruveOrderBuilderFactoryInterface::class);

        $lineItemOne = $this->createMock(PaymentLineItem::class);
        $lineItemTwo = $this->createMock(PaymentLineItem::class);

        $this->paymentContext->expects(self::once())
            ->method('getCurrency')
            ->willReturn(self::CURRENCY);
        $this->paymentContext->expects(self::once())
            ->method('getSourceEntityIdentifier')
            ->willReturn(self::MERCHANT_ORDER_ID);
        $this->paymentContext->expects(self::once())
            ->method('getLineItems')
            ->willReturn([$lineItemOne, $lineItemTwo]);

        $shippingAmountProvider = $this->createMock(ShippingAmountProviderInterface::class);
        $shippingAmountProvider->expects(self::once())
            ->method('getShippingAmount')
            ->with($this->paymentContext)
            ->willReturn(self::SHIPPING_AMOUNT);

        $taxAmountProvider = $this->createMock(TaxAmountProviderInterface::class);
        $taxAmountProvider->expects(self::once())
            ->method('getTaxAmount')
            ->with($this->paymentContext)
            ->willReturn(self::TAX_AMOUNT);

        $apruveLineItemFromPaymentLineItemFactory = $this->createMock(
            ApruveLineItemFromPaymentLineItemFactoryInterface::class
        );
        $apruveLineItemFromPaymentLineItemFactory->expects(self::exactly(2))
            ->method('createFromPaymentLineItem')
            ->willReturnMap([
                [$lineItemOne, $this->mockApruveLineItem(self::LINE_ITEMS['sku1'])],
                [$lineItemTwo, $this->mockApruveLineItem(self::LINE_ITEMS['sku2'])],
            ]);

        $this->factory = new ApruveOrderFromPaymentContextFactory(
            $this->mockAmountNormalizer(),
            $apruveLineItemFromPaymentLineItemFactory,
            $shippingAmountProvider,
            $taxAmountProvider,
            $this->totalProcessorProvider,
            $this->apruveOrderBuilderFactory
        );
    }

    public function testGetResult(): void
    {
        $this->apruveOrderBuilderFactory->expects(self::once())
            ->method('create')
            ->with(
                self::MERCHANT_ID,
                self::TOTAL_AMOUNT_CENTS,
                self::CURRENCY,
                [self::LINE_ITEMS['sku1'], self::LINE_ITEMS['sku2']]
            )
            ->willReturn($this->apruveOrderBuilder);

        $this->apruveOrderBuilder->expects(self::once())
            ->method('setMerchantOrderId')
            ->with(self::MERCHANT_ORDER_ID)
            ->willReturnSelf();

        $this->apruveOrderBuilder->expects(self::once())
            ->method('setShippingCents')
            ->with(self::SHIPPING_AMOUNT_CENTS)
            ->willReturnSelf();

        $this->apruveOrderBuilder->expects(self::once())
            ->method('setTaxCents')
            ->with(self::TAX_AMOUNT_CENTS)
            ->willReturnSelf();

        $this->apruveOrderBuilder->expects(self::once())
            ->method('setFinalizeOnCreate')
            ->with(self::FINALIZE_ON_CREATE)
            ->willReturnSelf();

        $this->apruveOrderBuilder->expects(self::once())
            ->method('setInvoiceOnCreate')
            ->with(self::INVOICE_ON_CREATE)
            ->willReturnSelf();

        $this->apruveOrderBuilder->expects(self::once())
            ->method('getResult');

        $someEntity = new \stdClass();
        $this->paymentContext->expects(self::once())
            ->method('getSourceEntity')
            ->willReturn($someEntity);

        $total = new Subtotal();
        $total->setAmount(self::TOTAL_AMOUNT_USD);

        $this->totalProcessorProvider->expects(self::once())
            ->method('getTotal')
            ->with($someEntity)
            ->willReturn($total);

        $this->factory->createFromPaymentContext($this->paymentContext, $this->mockApruveConfig());
    }

    private function mockApruveLineItem(array $apruveLineItemData): ApruveLineItem
    {
        $apruveLineItem = $this->createMock(ApruveLineItem::class);
        $apruveLineItem->expects(self::once())
            ->method('getData')
            ->willReturn($apruveLineItemData);

        return $apruveLineItem;
    }

    private function mockAmountNormalizer(): AmountNormalizerInterface
    {
        $amountNormalizer = $this->createMock(AmountNormalizerInterface::class);
        $amountNormalizer->expects(self::any())
            ->method('normalize')
            ->willReturnMap([
                [self::TOTAL_AMOUNT_USD, self::TOTAL_AMOUNT_CENTS],
                [self::SHIPPING_AMOUNT, self::SHIPPING_AMOUNT_CENTS],
                [self::TAX_AMOUNT, self::TAX_AMOUNT_CENTS],

            ]);

        return $amountNormalizer;
    }

    private function mockApruveConfig(): ApruveConfigInterface
    {
        $config = $this->createMock(ApruveConfigInterface::class);
        $config->expects(self::once())
            ->method('getMerchantId')
            ->willReturn(self::MERCHANT_ID);

        return $config;
    }
}
