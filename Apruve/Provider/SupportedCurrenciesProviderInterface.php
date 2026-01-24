<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Provider;

/**
 * Defines the contract for retrieving and validating supported currencies
 * in the Apruve payment integration.
 */
interface SupportedCurrenciesProviderInterface
{
    /**
     * @return string[]
     */
    public function getCurrencies();

    /**
     * @param string $currency
     *
     * @return bool
     */
    public function isSupported($currency);
}
