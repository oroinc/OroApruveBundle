<?php

namespace Oro\Bundle\ApruveBundle\Method\Config;

use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Defines the contract for Apruve payment method configuration.
 */
interface ApruveConfigInterface extends PaymentConfigInterface
{
    /**
     * @return bool
     */
    public function isTestMode();

    /**
     * @return string
     */
    public function getApiKey();

    /**
     * @return string
     */
    public function getMerchantId();
}
