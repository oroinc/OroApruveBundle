<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Connection\Validator\Result;

use Oro\Bundle\ApruveBundle\Connection\Validator\Result\ApruveConnectionValidatorResult;

class ApruveConnectionValidatorResultTest extends \PHPUnit\Framework\TestCase
{
    private array $parameters;
    private ApruveConnectionValidatorResult $connectionValidationResult;

    #[\Override]
    protected function setUp(): void
    {
        $this->parameters = [
            'status' => true,
            'error_severity' => 'error_severity',
            'error_message' => 'Wrong api key',
        ];
        $this->connectionValidationResult = new ApruveConnectionValidatorResult($this->parameters);
    }

    public function testGetters()
    {
        self::assertEquals($this->parameters['status'], $this->connectionValidationResult->getStatus());
        self::assertEquals(
            $this->parameters['error_severity'],
            $this->connectionValidationResult->getErrorSeverity()
        );
        self::assertEquals($this->parameters['error_message'], $this->connectionValidationResult->getErrorMessage());
    }
}
