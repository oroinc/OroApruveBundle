<?php

namespace Oro\Bundle\ApruveBundle\Client\Url\Provider;

/**
 * Defines the contract for providing Apruve API URLs.
 */
interface ApruveClientUrlProviderInterface
{
    /**
     * @param bool $isTestMode
     *
     * @return string
     */
    public function getApruveUrl($isTestMode);
}
