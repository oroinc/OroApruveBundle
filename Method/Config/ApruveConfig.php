<?php

namespace Oro\Bundle\ApruveBundle\Method\Config;

use Oro\Bundle\PaymentBundle\Method\Config\ParameterBag\AbstractParameterBagPaymentConfig;

class ApruveConfig extends AbstractParameterBagPaymentConfig implements ApruveConfigInterface
{
    /**
     * Apruve-specific parameters keys.
     */
    const TEST_MODE_KEY  = 'test_mode';
    const API_KEY_KEY  = 'api_key';
    const MERCHANT_ID_KEY  = 'merchant_id';

    /**
     * {@inheritDoc}
     */
    public function isTestMode()
    {
        return (bool)$this->get(self::TEST_MODE_KEY);
    }

    /**
     * {@inheritDoc}
     */
    public function getApiKey()
    {
        return (string)$this->get(self::API_KEY_KEY);
    }

    /**
     * {@inheritDoc}
     */
    public function getMerchantId()
    {
        return (string)$this->get(self::MERCHANT_ID_KEY);
    }
}
