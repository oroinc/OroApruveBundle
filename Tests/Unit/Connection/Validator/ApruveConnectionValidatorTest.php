<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Connection\Validator;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\Settings\ApruveSettingsRestClientFactoryInterface;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\ApruveBundle\Connection\Validator\ApruveConnectionValidator;
use Oro\Bundle\ApruveBundle\Connection\Validator\Request\Factory\ApruveConnectionValidatorRequestFactoryInterface;
use Oro\Bundle\ApruveBundle\Connection\Validator\Result\ApruveConnectionValidatorResultInterface;
use Oro\Bundle\ApruveBundle\Connection\Validator\Result\Factory\ApruveConnectionValidatorResultFactoryInterface;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Exception\RestException;
use Psr\Log\LoggerInterface;

class ApruveConnectionValidatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApruveConnectionValidatorRequestFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $requestFactory;

    /** @var ApruveSettingsRestClientFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $clientFactory;

    /** @var ApruveConnectionValidatorResultFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $resultFactory;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var ApruveConnectionValidator */
    private $validator;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(ApruveSettingsRestClientFactoryInterface::class);
        $this->requestFactory = $this->createMock(ApruveConnectionValidatorRequestFactoryInterface::class);
        $this->resultFactory = $this->createMock(ApruveConnectionValidatorResultFactoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->validator = new ApruveConnectionValidator(
            $this->clientFactory,
            $this->requestFactory,
            $this->resultFactory,
            $this->logger
        );
    }

    public function testValidateConnectionByApruveSettings()
    {
        $transport = new ApruveSettings();

        $request = $this->createMock(ApruveRequestInterface::class);
        $client = $this->createMock(ApruveRestClientInterface::class);
        $response = $this->createMock(RestResponseInterface::class);
        $result = $this->createMock(ApruveConnectionValidatorResultInterface::class);

        $this->requestFactory->expects(self::once())
            ->method('createBySettings')
            ->with($transport)
            ->willReturn($request);

        $this->clientFactory->expects(self::once())
            ->method('create')
            ->willReturn($client);

        $client->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($response);

        $this->resultFactory->expects(self::once())
            ->method('createResultByApruveClientResponse')
            ->willReturn($result);

        $this->logger->expects(self::never())
            ->method(self::anything());

        self::assertSame($result, $this->validator->validateConnectionByApruveSettings($transport));
    }

    public function testValidateConnectionByApruveSettingsServerError()
    {
        $transport = new ApruveSettings();

        $request = $this->createMock(ApruveRequestInterface::class);
        $client = $this->createMock(ApruveRestClientInterface::class);
        $result = $this->createMock(ApruveConnectionValidatorResultInterface::class);

        $this->requestFactory->expects(self::once())
            ->method('createBySettings')
            ->with($transport)
            ->willReturn($request);

        $this->clientFactory->expects(self::once())
            ->method('create')
            ->willReturn($client);

        $client->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willThrowException(new RestException('some error'));

        $this->resultFactory->expects(self::once())
            ->method('createExceptionResult')
            ->willReturn($result);

        $this->logger->expects(self::once())
            ->method('error')
            ->with('some error', []);

        self::assertSame($result, $this->validator->validateConnectionByApruveSettings($transport));
    }
}
