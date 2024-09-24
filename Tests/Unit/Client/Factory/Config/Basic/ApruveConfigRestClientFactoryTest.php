<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Factory\Config\Basic;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\ApruveRestClientFactoryInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\Config\Basic\ApruveConfigRestClientFactory;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;

class ApruveConfigRestClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApruveRestClientFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $restClientFactory;

    /** @var ApruveConfigRestClientFactory */
    private $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->restClientFactory = $this->createMock(ApruveRestClientFactoryInterface::class);

        $this->factory = new ApruveConfigRestClientFactory($this->restClientFactory);
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(bool $isTestMode)
    {
        $apruveConfig = $this->createMock(ApruveConfigInterface::class);

        $apiKey = 'qwerty12345';

        $apruveConfig->expects(self::once())
            ->method('getApiKey')
            ->willReturn($apiKey);

        $apruveConfig->expects(self::once())
            ->method('isTestMode')
            ->willReturn($isTestMode);

        $expectedClient = $this->createMock(ApruveRestClientInterface::class);

        $this->restClientFactory->expects(self::once())
            ->method('create')
            ->with($apiKey, $isTestMode)
            ->willReturn($expectedClient);

        self::assertEquals($expectedClient, $this->factory->create($apruveConfig));
    }

    public function createDataProvider(): array
    {
        return [
            'test mode' => [
                'isTestMode' => true,
            ],
            'prod mode' => [
                'isTestMode' => false,
            ],
        ];
    }
}
