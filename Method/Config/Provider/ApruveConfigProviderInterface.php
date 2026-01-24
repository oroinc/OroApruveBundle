<?php

namespace Oro\Bundle\ApruveBundle\Method\Config\Provider;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;

/**
 * Defines the contract for providing Apruve configuration.
 */
interface ApruveConfigProviderInterface
{
    /**
     * @return ApruveConfigInterface[]
     */
    public function getPaymentConfigs();

    /**
     * @param string $identifier
     *
     * @return ApruveConfigInterface|null
     */
    public function getPaymentConfig($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPaymentConfig($identifier);
}
