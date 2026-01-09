<?php

namespace Oro\Bundle\ApruveBundle\Services;

use Symfony\Component\HttpFoundation\Request;

/**
 * Defines the contract for verifying Apruve webhook signatures.
 */
interface SignatureVerifyingServiceInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function verifyRequestSignature(Request $request);
}
