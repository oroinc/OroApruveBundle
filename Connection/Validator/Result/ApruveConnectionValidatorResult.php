<?php

namespace Oro\Bundle\ApruveBundle\Connection\Validator\Result;

use Symfony\Component\HttpFoundation\ParameterBag;

class ApruveConnectionValidatorResult extends ParameterBag implements ApruveConnectionValidatorResultInterface
{
    const STATUS_KEY = 'status';
    const ERROR_SEVERITY_KEY = 'error_severity';
    const ERROR_MESSAGE_KEY = 'error_message';

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
