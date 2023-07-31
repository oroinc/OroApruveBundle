<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProviderInterface;
use Oro\Bundle\ApruveBundle\Provider\TaxAmountProviderInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItem;
use Oro\Bundle\PricingBundle\SubtotalProcessor\TotalProcessorProvider;

/**
 * Factory that has common methods to get tax, shipping information, apruve line items
 */
abstract class AbstractApruveEntityWithLineItemsFactory extends AbstractApruveEntityFactory
{
    /**
     * @var ShippingAmountProviderInterface
     */
    protected $shippingAmountProvider;

    /**
     * @var TaxAmountProviderInterface
     */
    protected $taxAmountProvider;

    /**
     * @var ApruveLineItemFromPaymentLineItemFactoryInterface
     */
    protected $apruveLineItemFromPaymentLineItemFactory;

    /**
     * @var TotalProcessorProvider
     */
    protected $totalProcessorProvider;

    public function __construct(
        AmountNormalizerInterface $amountNormalizer,
        ApruveLineItemFromPaymentLineItemFactoryInterface $apruveLineItemFromPaymentLineItemFactory,
        ShippingAmountProviderInterface $shippingAmountProvider,
        TaxAmountProviderInterface $taxAmountProvider,
        TotalProcessorProvider $totalProcessorProvider
    ) {
        parent::__construct($amountNormalizer);

        $this->apruveLineItemFromPaymentLineItemFactory = $apruveLineItemFromPaymentLineItemFactory;
        $this->shippingAmountProvider = $shippingAmountProvider;
        $this->taxAmountProvider = $taxAmountProvider;
        $this->totalProcessorProvider = $totalProcessorProvider;
    }

    /**
     * @param Collection<PaymentLineItem> $lineItems
     *
     * @return array
     */
    protected function getLineItems($lineItems)
    {
        $apruveLineItems = [];
        foreach ($lineItems as $lineItem) {
            $apruveLineItems[] = $this->apruveLineItemFromPaymentLineItemFactory
                ->createFromPaymentLineItem($lineItem)
                ->getData();
        }

        return $apruveLineItems;
    }

    /**
     * @param PaymentContextInterface $paymentContext
     *
     * @return int
     */
    protected function getShippingCents(PaymentContextInterface $paymentContext)
    {
        $amount = $this->shippingAmountProvider->getShippingAmount($paymentContext);

        return $this->normalizeAmount($amount);
    }

    /**
     * Get tax amount in cents as integer
     * or null in case of it's impossible to get tax amount
     *
     * @param PaymentContextInterface $paymentContext
     *
     * @return int|null
     */
    protected function getTaxCents(PaymentContextInterface $paymentContext)
    {
        $amount = $this->taxAmountProvider->getTaxAmount($paymentContext);

        if ($amount === null) {
            // Can not get tax amount according to payment context
            return null;
        }

        return $this->normalizeAmount($amount);
    }

    /**
     * Get total amount for "amount_cents" property.
     * Sums total price of line items, shipping costs and taxes.
     *
     * @param PaymentContextInterface $paymentContext
     *
     * @return int
     */
    protected function getAmountCents(PaymentContextInterface $paymentContext)
    {
        $totalAmount = $this->totalProcessorProvider->getTotal($paymentContext->getSourceEntity());
        $amountCents = $this->normalizePrice($totalAmount->getTotalPrice());

        return $amountCents;
    }
}
