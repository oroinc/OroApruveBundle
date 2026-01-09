<?php

namespace Oro\Bundle\ApruveBundle\Client\Factory\Settings;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;

/**
 * Defines the contract for creating Apruve REST client instances with settings.
 */
interface ApruveSettingsRestClientFactoryInterface
{
    /**
     * @param ApruveSettings $apruveSettings
     *
     * @return ApruveRestClientInterface
     */
    public function create(ApruveSettings $apruveSettings);
}
