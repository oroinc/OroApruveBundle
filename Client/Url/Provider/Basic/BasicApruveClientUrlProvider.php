<?php

namespace Oro\Bundle\ApruveBundle\Client\Url\Provider\Basic;

use Oro\Bundle\ApruveBundle\Client\Url\Provider\ApruveClientUrlProviderInterface;

class BasicApruveClientUrlProvider implements ApruveClientUrlProviderInterface
{
    /**
     * @internal
     */
    public const BASE_URL_PROD = 'https://app.apruve.com/api/v4/';

    /**
     * @internal
     */
    public const BASE_URL_TEST = 'https://test.apruve.com/api/v4/';

    #[\Override]
    public function getApruveUrl($isTestMode)
    {
        if ($isTestMode) {
            return self::BASE_URL_TEST;
        }

        return self::BASE_URL_PROD;
    }
}
