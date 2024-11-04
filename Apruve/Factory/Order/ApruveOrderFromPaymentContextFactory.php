<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\Order;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Order\ApruveOrderBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\AbstractApruveEntityWithLineItemsFactory;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProviderInterface;
use Oro\Bundle\ApruveBundle\Provider\TaxAmountProviderInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PricingBundle\SubtotalProcessor\TotalProcessorProvider;

/**
 * Factory creates apruve order model based on payment context
 */
class ApruveOrderFromPaymentContextFactory extends AbstractApruveEntityWithLineItemsFactory implements
    ApruveOrderFromPaymentContextFactoryInterface
{
    private const FINALIZE_ON_CREATE = true;
    private const INVOICE_ON_CREATE = false;

    /**
     * @var ApruveOrderBuilderFactoryInterface
     */
    private $apruveOrderBuilderFactory;

    public function __construct(
        AmountNormalizerInterface $amountNormalizer,
        ApruveLineItemFromPaymentLineItemFactoryInterface $apruveLineItemFromPaymentLineItemFactory,
        ShippingAmountProviderInterface $shippingAmountProvider,
        TaxAmountProviderInterface $taxAmountProvider,
        TotalProcessorProvider $totalProcessorProvider,
        ApruveOrderBuilderFactoryInterface $apruveOrderBuilderFactory
    ) {
        parent::__construct(
            $amountNormalizer,
            $apruveLineItemFromPaymentLineItemFactory,
            $shippingAmountProvider,
            $taxAmountProvider,
            $totalProcessorProvider
        );

        $this->apruveOrderBuilderFactory = $apruveOrderBuilderFactory;
    }

    #[\Override]
    public function createFromPaymentContext(
        PaymentContextInterface $paymentContext,
        ApruveConfigInterface $apruveConfig
    ) {
        $apruveOrderBuilder = $this->apruveOrderBuilderFactory
            ->create(
                $apruveConfig->getMerchantId(),
                $this->getAmountCents($paymentContext),
                $paymentContext->getCurrency(),
                $this->getLineItems($paymentContext->getLineItems())
            );

        $apruveOrderBuilder
            ->setMerchantOrderId($this->getMerchantOrderId($paymentContext))
            ->setShippingCents($this->getShippingCents($paymentContext));

        $taxCents = $this->getTaxCents($paymentContext);
        if ($taxCents !== null) {
            $apruveOrderBuilder->setTaxCents($taxCents);
        }

        $apruveOrderBuilder
            ->setFinalizeOnCreate(self::FINALIZE_ON_CREATE)
            ->setInvoiceOnCreate(self::INVOICE_ON_CREATE);

        return $apruveOrderBuilder->getResult();
    }

    /**
     * @param PaymentContextInterface $paymentContext
     *
     * @return string
     */
    private function getMerchantOrderId(PaymentContextInterface $paymentContext)
    {
        return (string)$paymentContext->getSourceEntityIdentifier();
    }
}
