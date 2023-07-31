<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem;

use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\AbstractApruveEntityFactory;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItem;
use Oro\Bundle\ProductBundle\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Creates {@see ApruveLineItem} from {@see PaymentLineItem}
 */
class ApruveLineItemFromPaymentLineItemFactory extends AbstractApruveEntityFactory implements
    ApruveLineItemFromPaymentLineItemFactoryInterface
{
    private ApruveLineItemBuilderFactoryInterface $apruveLineItemBuilderFactory;

    private RouterInterface $router;

    public function __construct(
        AmountNormalizerInterface $amountNormalizer,
        ApruveLineItemBuilderFactoryInterface $apruveLineItemBuilderFactory,
        RouterInterface $router
    ) {
        parent::__construct($amountNormalizer);

        $this->apruveLineItemBuilderFactory = $apruveLineItemBuilderFactory;
        $this->router = $router;
    }

    public function createFromPaymentLineItem(PaymentLineItem $paymentLineItem): ApruveLineItem
    {
        $apruveLineItemBuilder = $this->apruveLineItemBuilderFactory
            ->create(
                $this->getTitle($paymentLineItem),
                $this->getAmountCents($paymentLineItem),
                $paymentLineItem->getQuantity(),
                $paymentLineItem->getPrice()->getCurrency()
            );

        $apruveLineItemBuilder
            ->setEaCents($this->normalizePrice($paymentLineItem->getPrice()))
            ->setSku($this->getSku($paymentLineItem));

        $product = $paymentLineItem->getProduct();
        if ($product instanceof Product) {
            $apruveLineItemBuilder
                ->setDescription($this->getDescription($product))
                ->setViewProductUrl($this->getViewProductUrl($product));
        }

        return $apruveLineItemBuilder->getResult();
    }

    private function getTitle(PaymentLineItem $lineItem): string
    {
        $product = $lineItem->getProduct();
        // Product is optional PaymentLineItem.
        if ($product !== null) {
            $title = $product->getName();
        } else {
            // ... though title is required by Apruve, so use SKU when no product is available.
            $title = $this->getSku($lineItem);
        }

        return $this->sanitizeText($title);
    }

    private function getAmountCents(PaymentLineItem $lineItem): int
    {
        $amount = (float)$lineItem->getPrice()->getValue();
        $quantity = $lineItem->getQuantity();

        return $this->normalizeAmount($amount * $quantity);
    }

    private function getSku(PaymentLineItem $lineItem): string
    {
        $sku = $lineItem->getProductSku();

        // Product sku is optional, and will be null if not provided to builder.
        if ($sku === null) {
            // Try to fetch it directly from product.
            $product = $lineItem->getProduct();
            // ... though it is optional as well.
            if ($product !== null) {
                $sku = $product->getSku();
            }
        }

        return (string)$sku;
    }

    private function getDescription(Product $product): string
    {
        $description = (string)$product->getDescription();

        return $this->sanitizeText($description);
    }

    /**
     * Sanitize text for Apruve.
     *
     * Apruve order data will be used to generate secure hash for Apruve, that is why
     * we have to make it suitable for hash generation on Apruve side.
     *
     *  Line breaks should be removed from both the secure_hash input string
     *  and the order description. You just have to ensure that they match,
     *  so if you replace newlines with spaces you should do the same in both places.
     *  (c) Apruve Support, request #359
     *
     * @param string $description
     *
     * @return string
     */
    private function sanitizeText(string $description): string
    {
        $description = strip_tags($description);

        return trim(str_replace(PHP_EOL, ' ', $description));
    }

    private function getViewProductUrl(Product $product): string
    {
        return $this->router->generate(
            'oro_product_frontend_product_view',
            ['id' => $product->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
