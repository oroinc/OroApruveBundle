<?php

namespace Oro\Bundle\ApruveBundle\Provider;

use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;

/**
 * Defines the contract for providing shipping amounts.
 */
interface ShippingAmountProviderInterface
{
    /**
     * @param PaymentContextInterface $paymentContext
     *
     * @return float
     */
    public function getShippingAmount(PaymentContextInterface $paymentContext);
}
