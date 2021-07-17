<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice\ApruveInvoiceBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\AbstractApruveEntityWithLineItemsFactory;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProviderInterface;
use Oro\Bundle\ApruveBundle\Provider\TaxAmountProviderInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PricingBundle\SubtotalProcessor\TotalProcessorProvider;

/**
 * Creates apruve invoice according to payment context using apruve invoice builder
 */
class ApruveInvoiceFromPaymentContextFactory extends AbstractApruveEntityWithLineItemsFactory implements
    ApruveInvoiceFromPaymentContextFactoryInterface
{
    /**
     * @internal
     */
    const ISSUE_ON_CREATE = true;

    /**
     * @var ApruveInvoiceBuilderFactoryInterface
     */
    private $apruveInvoiceBuilderFactory;

    public function __construct(
        AmountNormalizerInterface $amountNormalizer,
        ApruveLineItemFromPaymentLineItemFactoryInterface $apruveLineItemFromPaymentLineItemFactory,
        ShippingAmountProviderInterface $shippingAmountProvider,
        TaxAmountProviderInterface $taxAmountProvider,
        TotalProcessorProvider $totalProcessorProvider,
        ApruveInvoiceBuilderFactoryInterface $apruveInvoiceBuilderFactory
    ) {
        parent::__construct(
            $amountNormalizer,
            $apruveLineItemFromPaymentLineItemFactory,
            $shippingAmountProvider,
            $taxAmountProvider,
            $totalProcessorProvider
        );

        $this->apruveInvoiceBuilderFactory = $apruveInvoiceBuilderFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromPaymentContext(PaymentContextInterface $paymentContext)
    {
        $apruveInvoiceBuilder = $this->apruveInvoiceBuilderFactory
            ->create(
                $this->getAmountCents($paymentContext),
                $paymentContext->getCurrency(),
                $this->getLineItems($paymentContext->getLineItems())
            );

        $apruveInvoiceBuilder
            ->setShippingCents($this->getShippingCents($paymentContext));

        $taxCents = $this->getTaxCents($paymentContext);
        if ($taxCents !== null) {
            $apruveInvoiceBuilder->setTaxCents($taxCents);
        }

        $apruveInvoiceBuilder
            ->setIssueOnCreate(self::ISSUE_ON_CREATE);

        return $apruveInvoiceBuilder->getResult();
    }
}
