<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment\ApruveShipmentBuilderFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Factory\AbstractApruveEntityWithLineItemsFactory;
use Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem\ApruveLineItemFromPaymentLineItemFactoryInterface;
use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProviderInterface;
use Oro\Bundle\ApruveBundle\Provider\TaxAmountProviderInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;

class ApruveShipmentFromPaymentContextFactory extends AbstractApruveEntityWithLineItemsFactory implements
    ApruveShipmentFromPaymentContextFactoryInterface
{
    /**
     * @var ApruveShipmentBuilderFactoryInterface
     */
    private $apruveShipmentBuilderFactory;

    /**
     * @var ShippingMethodProviderInterface
     */
    private $shippingMethodProvider;

    /**
     * @param AmountNormalizerInterface                         $amountNormalizer
     * @param ApruveLineItemFromPaymentLineItemFactoryInterface $apruveLineItemFromPaymentLineItemFactory
     * @param ShippingAmountProviderInterface                   $shippingAmountProvider
     * @param TaxAmountProviderInterface                        $taxAmountProvider
     * @param ApruveShipmentBuilderFactoryInterface             $apruveShipmentBuilderFactory
     * @param ShippingMethodProviderInterface                   $shippingMethodProvider
     */
    public function __construct(
        AmountNormalizerInterface $amountNormalizer,
        ApruveLineItemFromPaymentLineItemFactoryInterface $apruveLineItemFromPaymentLineItemFactory,
        ShippingAmountProviderInterface $shippingAmountProvider,
        TaxAmountProviderInterface $taxAmountProvider,
        ApruveShipmentBuilderFactoryInterface $apruveShipmentBuilderFactory,
        ShippingMethodProviderInterface $shippingMethodProvider
    ) {
        parent::__construct(
            $amountNormalizer,
            $apruveLineItemFromPaymentLineItemFactory,
            $shippingAmountProvider,
            $taxAmountProvider
        );

        $this->apruveShipmentBuilderFactory = $apruveShipmentBuilderFactory;
        $this->shippingMethodProvider = $shippingMethodProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromPaymentContext(PaymentContextInterface $paymentContext)
    {
        $apruveShipmentBuilder = $this->apruveShipmentBuilderFactory
            ->create(
                $this->getAmountCents($paymentContext),
                $paymentContext->getCurrency(),
                $this->getShippedAt()
            );

        $apruveShipmentBuilder
            ->setLineItems($this->getLineItems($paymentContext->getLineItems()))
            ->setShippingCents($this->getShippingCents($paymentContext))
            ->setTaxCents($this->getTaxCents($paymentContext));

        $shippingMethodId = $paymentContext->getShippingMethod();
        if ($this->shippingMethodProvider->hasShippingMethod($shippingMethodId)) {
            $shippingMethod = $this->shippingMethodProvider
                ->getShippingMethod($shippingMethodId);

            $apruveShipmentBuilder->setShipper($shippingMethod->getLabel());
        }

        return $apruveShipmentBuilder->getResult();
    }

    /**
     * @return string
     */
    private function getShippedAt()
    {
        return (new \DateTime())->format(\DateTime::ATOM);
    }
}
