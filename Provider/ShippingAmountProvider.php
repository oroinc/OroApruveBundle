<?php

namespace Oro\Bundle\ApruveBundle\Provider;

use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Provider\SurchargeProvider;

class ShippingAmountProvider implements ShippingAmountProviderInterface
{
    /**
     * @var SurchargeProvider
     */
    private $surchargeProvider;

    public function __construct(SurchargeProvider $surchargeProvider)
    {
        $this->surchargeProvider = $surchargeProvider;
    }

    #[\Override]
    public function getShippingAmount(PaymentContextInterface $paymentContext)
    {
        $surcharge = $this->surchargeProvider->getSurcharges($paymentContext->getSourceEntity());

        return (float) $surcharge->getShippingAmount();
    }
}
