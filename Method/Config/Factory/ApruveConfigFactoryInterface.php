<?php

namespace Oro\Bundle\ApruveBundle\Method\Config\Factory;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;

/**
 * Defines the contract for creating Apruve configuration instances.
 */
interface ApruveConfigFactoryInterface
{
    /**
     * @param ApruveSettings $settings
     *
     * @return ApruveConfigInterface
     */
    public function create(ApruveSettings $settings);
}
