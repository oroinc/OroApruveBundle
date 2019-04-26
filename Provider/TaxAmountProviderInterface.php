<?php

namespace Oro\Bundle\ApruveBundle\Provider;

use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;

/**
 * TaxAmountProvider provides a way to interact with tax calculation system
 */
interface TaxAmountProviderInterface
{
    /**
     * Get tax amount based on PaymentContext
     *
     * Returns float with tax amount or null in case of calculation can not be done
     *
     * @param PaymentContextInterface $paymentContext
     *
     * @return float|null
     */
    public function getTaxAmount(PaymentContextInterface $paymentContext);
}
