<?php

namespace Oro\Bundle\ApruveBundle\Method\Config;

use Oro\Bundle\PaymentBundle\Method\Config\ParameterBag\AbstractParameterBagPaymentConfig;

class ApruveConfig extends AbstractParameterBagPaymentConfig implements ApruveConfigInterface
{
    /**
     * Apruve-specific parameters keys.
     */
    public const TEST_MODE_KEY  = 'test_mode';
    public const API_KEY_KEY  = 'api_key';
    public const MERCHANT_ID_KEY  = 'merchant_id';

    #[\Override]
    public function isTestMode()
    {
        return (bool)$this->get(self::TEST_MODE_KEY);
    }

    #[\Override]
    public function getApiKey()
    {
        return (string)$this->get(self::API_KEY_KEY);
    }

    #[\Override]
    public function getMerchantId()
    {
        return (string)$this->get(self::MERCHANT_ID_KEY);
    }
}
