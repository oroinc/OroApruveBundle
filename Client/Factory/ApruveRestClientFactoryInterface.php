<?php

namespace Oro\Bundle\ApruveBundle\Client\Factory;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;

/**
 * Defines the contract for creating Apruve REST client instances.
 */
interface ApruveRestClientFactoryInterface
{
    /**
     * @param string $apiKey
     * @param bool   $isTestMode
     *
     * @return ApruveRestClientInterface
     */
    public function create($apiKey, $isTestMode);
}
