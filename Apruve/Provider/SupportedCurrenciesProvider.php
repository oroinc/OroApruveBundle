<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Provider;

/**
 * Defines and validates currencies supported by the Apruve payment integration (currently USD only).
 */
class SupportedCurrenciesProvider implements SupportedCurrenciesProviderInterface
{
    /**
     * Apruve supports only USD for now.
     */
    public const USD = 'USD';

    #[\Override]
    public function getCurrencies()
    {
        return [self::USD];
    }

    #[\Override]
    public function isSupported($currency)
    {
        return in_array($currency, $this->getCurrencies(), true);
    }
}
