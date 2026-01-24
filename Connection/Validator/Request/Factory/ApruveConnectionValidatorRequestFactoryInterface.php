<?php

namespace Oro\Bundle\ApruveBundle\Connection\Validator\Request\Factory;

use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;

/**
 * Defines the contract for creating connection validator requests.
 */
interface ApruveConnectionValidatorRequestFactoryInterface
{
    /**
     * @param Apruvesettings $settings
     *
     * @return ApruveRequestInterface
     */
    public function createBySettings(ApruveSettings $settings);
}
