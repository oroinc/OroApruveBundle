<?php

namespace Oro\Bundle\ApruveBundle\Services;

use Oro\Bundle\ApruveBundle\Provider\ApruvePublicKeyProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Verifies the authenticity of Apruve webhook requests.
 */
class SignatureVerifyingService implements SignatureVerifyingServiceInterface
{
    public const SIGNATURE_HEADER_NAME = 'X-Apruve-Signature';

    /**
     * @internal
     */
    public const SIGNATURE_ALGORITHM = OPENSSL_ALGO_SHA256;

    /**
     * @var ApruvePublicKeyProviderInterface
     */
    private $apruvePublicKeyProvider;

    public function __construct(ApruvePublicKeyProviderInterface $apruvePublicKeyProvider)
    {
        $this->apruvePublicKeyProvider = $apruvePublicKeyProvider;
    }

    #[\Override]
    public function verifyRequestSignature(Request $request)
    {
        $decodedSignuture = $request->headers->get(self::SIGNATURE_HEADER_NAME);

        $signature = base64_decode($decodedSignuture);

        $result = openssl_verify(
            $request->getContent(),
            $signature,
            $this->apruvePublicKeyProvider->getPublicKey(),
            self::SIGNATURE_ALGORITHM
        );

        return $result === 1;
    }
}
