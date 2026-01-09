<?php

namespace Oro\Bundle\ApruveBundle\Connection\Validator\Result;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Represents the result of an Apruve connection validation.
 */
class ApruveConnectionValidatorResult extends ParameterBag implements ApruveConnectionValidatorResultInterface
{
    public const STATUS_KEY = 'status';
    public const ERROR_SEVERITY_KEY = 'error_severity';
    public const ERROR_MESSAGE_KEY = 'error_message';

    #[\Override]
    public function getStatus()
    {
        return (bool)$this->get(self::STATUS_KEY);
    }

    #[\Override]
    public function getErrorSeverity()
    {
        return (string)$this->get(self::ERROR_SEVERITY_KEY);
    }

    #[\Override]
    public function getErrorMessage()
    {
        return $this->get(self::ERROR_MESSAGE_KEY);
    }
}
