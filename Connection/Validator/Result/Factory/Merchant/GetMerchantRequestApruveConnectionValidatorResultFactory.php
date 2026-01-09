<?php

namespace Oro\Bundle\ApruveBundle\Connection\Validator\Result\Factory\Merchant;

use Oro\Bundle\ApruveBundle\Connection\Validator\Result\ApruveConnectionValidatorResult;
use Oro\Bundle\ApruveBundle\Connection\Validator\Result\Factory\ApruveConnectionValidatorResultFactoryInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Exception\RestException;

/**
 * Creates connection validator results from merchant request responses.
 */
class GetMerchantRequestApruveConnectionValidatorResultFactory implements
    ApruveConnectionValidatorResultFactoryInterface
{
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_NOT_FOUND = 404;

    public const INVALID_API_KEY_SEVERITY = 'authentication';
    public const MERCHANT_NOT_FOUND_SEVERITY = 'merchant';
    public const SERVER_SEVERITY = 'server';

    #[\Override]
    public function createResultByApruveClientResponse(RestResponseInterface $response)
    {
        $resultParams = [
            ApruveConnectionValidatorResult::STATUS_KEY => true,
            ApruveConnectionValidatorResult::ERROR_SEVERITY_KEY => null,
            ApruveConnectionValidatorResult::ERROR_MESSAGE_KEY => null,
        ];

        return new ApruveConnectionValidatorResult($resultParams);
    }

    #[\Override]
    public function createExceptionResult(RestException $exception)
    {
        $severity = self::SERVER_SEVERITY;
        switch ($exception->getCode()) {
            case self::HTTP_UNAUTHORIZED:
                $severity = self::INVALID_API_KEY_SEVERITY;
                break;
            case self::HTTP_NOT_FOUND:
                $severity = self::MERCHANT_NOT_FOUND_SEVERITY;
                break;
        }

        return new ApruveConnectionValidatorResult([
            ApruveConnectionValidatorResult::STATUS_KEY => false,
            ApruveConnectionValidatorResult::ERROR_SEVERITY_KEY => $severity,
            ApruveConnectionValidatorResult::ERROR_MESSAGE_KEY => $exception->getMessage(),
        ]);
    }
}
