<?php

namespace Oro\Bundle\ApruveBundle\Provider;

/**
 * Defines the contract for providing Apruve public keys.
 */
interface ApruvePublicKeyProviderInterface
{
    /**
     * @return string
     */
    public function getPublicKey();
}
