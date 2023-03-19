<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\Config;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfig;

class ApruveConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testGetters()
    {
        $adminLabel = 'Apruve';
        $label = 'Apruve';
        $shortLabel = 'Apruve (short)';
        $paymentMethodIdentifier = 'apruve_1';
        $testMode = false;
        $apiKey = '213a9079914f3b5163c6190f31444528';
        $merchantId = '7b97ea0172e18cbd4d3bf21e2b525b2d';

        $parameterBag = new ApruveConfig(
            [
                ApruveConfig::FIELD_ADMIN_LABEL => $adminLabel,
                ApruveConfig::FIELD_LABEL => $label,
                ApruveConfig::FIELD_SHORT_LABEL => $shortLabel,
                ApruveConfig::FIELD_PAYMENT_METHOD_IDENTIFIER => $paymentMethodIdentifier,
                ApruveConfig::API_KEY_KEY => $apiKey,
                ApruveConfig::MERCHANT_ID_KEY => $merchantId,
                ApruveConfig::TEST_MODE_KEY => $testMode,
            ]
        );

        self::assertEquals($adminLabel, $parameterBag->getAdminLabel());
        self::assertEquals($label, $parameterBag->getLabel());
        self::assertEquals($shortLabel, $parameterBag->getShortLabel());
        self::assertEquals($paymentMethodIdentifier, $parameterBag->getPaymentMethodIdentifier());
        self::assertEquals($apiKey, $parameterBag->getApiKey());
        self::assertEquals($merchantId, $parameterBag->getMerchantId());
        self::assertEquals($testMode, $parameterBag->isTestMode());
    }
}
