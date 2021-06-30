<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Factory\LineItem;

use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilderInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactory;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItemInterface;
use Oro\Bundle\ProductBundle\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ApruveLineItemFromPaymentLineItemFactoryTest extends \PHPUnit\Framework\TestCase
{
    private const PRODUCT_ID = 1;
    private const AMOUNT = 123.4;
    private const QUANTITY = 10;
    private const AMOUNT_CENTS = 12340;
    private const AMOUNT_EA = '12.34';
    private const AMOUNT_EA_CENTS = 1234;
    private const PRODUCT_SKU = 'sku1';
    private const LINE_ITEM_SKU = 'lineItemSku1';
    private const CURRENCY = 'USD';
    private const PRODUCT_NAME = 'Sample name';
    private const PRODUCT_DESCR = ' Sample description with' . PHP_EOL . 'line breaks and <div>tags</div>';
    private const PRODUCT_DESCR_SANITIZED = 'Sample description with line breaks and tags';
    private const VIEW_PRODUCT_URL = 'http://example.com/product/view/1';

    /** @var ApruveLineItemBuilderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveLineItemBuilder;

    /** @var ApruveLineItemBuilderFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveLineItemBuilderFactory;

    /** @var PaymentLineItemInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentLineItem;

    /** @var RouterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $router;

    /** @var ApruveLineItemFromPaymentLineItemFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->paymentLineItem = $this->createMock(PaymentLineItemInterface::class);
        $this->apruveLineItemBuilder = $this->createMock(ApruveLineItemBuilderInterface::class);
        $this->apruveLineItemBuilderFactory = $this->createMock(ApruveLineItemBuilderFactoryInterface::class);
        $this->router = $this->createMock(RouterInterface::class);

        $amountNormalizer = $this->createMock(AmountNormalizerInterface::class);
        $amountNormalizer->expects(self::any())
            ->method('normalize')
            ->willReturnMap([
                [self::AMOUNT, self::AMOUNT_CENTS],
                [self::AMOUNT_EA, self::AMOUNT_EA_CENTS],
            ]);

        $this->factory = new ApruveLineItemFromPaymentLineItemFactory(
            $amountNormalizer,
            $this->apruveLineItemBuilderFactory,
            $this->router
        );
    }

    /**
     * @dataProvider createFromPaymentLineItemDataProvider
     */
    public function testCreateFromPaymentLineItem(
        Product $product,
        string $title,
        ?string $lineItemSku,
        string $expectedSku
    ) {
        $this->mockRouter();
        $this->mockPaymentLineItem($product, $lineItemSku);

        $this->apruveLineItemBuilderFactory->expects(self::once())
            ->method('create')
            ->with($title, self::AMOUNT_CENTS, self::QUANTITY, self::CURRENCY)
            ->willReturn($this->apruveLineItemBuilder);

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('setEaCents')
            ->with(self::AMOUNT_EA_CENTS)
            ->willReturnSelf();

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('setSku')
            ->with($expectedSku);

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('setDescription')
            ->with(self::PRODUCT_DESCR_SANITIZED)
            ->willReturnSelf();

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('setViewProductUrl')
            ->with(self::VIEW_PRODUCT_URL);

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('getResult');

        $this->factory->createFromPaymentLineItem($this->paymentLineItem);
    }

    public function createFromPaymentLineItemDataProvider(): array
    {
        return [
            'line item sku is not null' => [
                'product' => $this->getProduct(),
                'title' => self::PRODUCT_NAME,
                'lineItemSku' => self::LINE_ITEM_SKU,
                'expectedSku' => self::LINE_ITEM_SKU,
            ],
            'line item sku is null' => [
                'product' => $this->getProduct(),
                'title' => self::PRODUCT_NAME,
                'lineItemSku' => null,
                'expectedSku' => self::PRODUCT_SKU,
            ],
        ];
    }

    public function testCreateFromPaymentLineItemIfNoProduct()
    {
        $this->mockPaymentLineItem(null, self::LINE_ITEM_SKU);

        $this->apruveLineItemBuilderFactory->expects(self::once())
            ->method('create')
            ->with(self::LINE_ITEM_SKU, self::AMOUNT_CENTS, self::QUANTITY, self::CURRENCY)
            ->willReturn($this->apruveLineItemBuilder);

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('setEaCents')
            ->with(self::AMOUNT_EA_CENTS)
            ->willReturnSelf();

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('setSku')
            ->with(self::LINE_ITEM_SKU);

        $this->apruveLineItemBuilder->expects(self::never())
            ->method('setDescription')
            ->willReturnSelf();

        $this->apruveLineItemBuilder->expects(self::never())
            ->method('setViewProductUrl');

        $this->apruveLineItemBuilder->expects(self::once())
            ->method('getResult');

        $this->factory->createFromPaymentLineItem($this->paymentLineItem);
    }

    private function getProduct(): Product
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getSku'])
            ->addMethods(['getName', 'getDescription'])
            ->getMock();
        $product->expects(self::any())
            ->method('getId')
            ->willReturn(self::PRODUCT_ID);
        $product->expects(self::any())
            ->method('getName')
            ->willReturn(self::PRODUCT_NAME);
        $product->expects(self::any())
            ->method('getDescription')
            ->willReturn(self::PRODUCT_DESCR);
        $product->expects(self::any())
            ->method('getSku')
            ->willReturn(self::PRODUCT_SKU);

        return $product;
    }

    private function mockRouter()
    {
        $this->router->expects(self::any())
            ->method('generate')
            ->with(
                'oro_product_frontend_product_view',
                ['id' => self::PRODUCT_ID],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn(self::VIEW_PRODUCT_URL);
    }

    private function mockPaymentLineItem(?Product $product, ?string $lineItemSku)
    {
        $price = $this->createMock(Price::class);
        $price->expects(self::any())
            ->method('getValue')
            ->willReturn(self::AMOUNT_EA);
        $price->expects(self::any())
            ->method('getCurrency')
            ->willReturn(self::CURRENCY);

        $this->paymentLineItem->expects(self::any())
            ->method('getPrice')
            ->willReturn($price);
        $this->paymentLineItem->expects(self::any())
            ->method('getQuantity')
            ->willReturn(self::QUANTITY);

        $this->paymentLineItem->expects(self::any())
            ->method('getProductSku')
            ->willReturn($lineItemSku);

        $this->paymentLineItem->expects(self::any())
            ->method('getProduct')
            ->willReturn($product);
    }
}
