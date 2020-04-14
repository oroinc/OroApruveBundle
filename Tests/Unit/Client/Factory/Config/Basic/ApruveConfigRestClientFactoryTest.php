<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Factory\Config\Basic;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\ApruveRestClientFactoryInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\Config\Basic\ApruveConfigRestClientFactory;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;

class ApruveConfigRestClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ApruveRestClientFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $restClientFactory;

    /**
     * @var ApruveConfigRestClientFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->restClientFactory = $this->createMock(ApruveRestClientFactoryInterface::class);

        $this->factory = new ApruveConfigRestClientFactory($this->restClientFactory);
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param bool $isTestMode
     */
    public function testCreate($isTestMode)
    {
        $apruveConfig = $this->getApruveConfigMock();

        $apiKey = 'qwerty12345';

        $apruveConfig->expects(static::once())
            ->method('getApiKey')
            ->willReturn($apiKey);

        $apruveConfig->expects(static::once())
            ->method('isTestMode')
            ->willReturn($isTestMode);

        $expectedClient = $this->createMock(ApruveRestClientInterface::class);

        $this->restClientFactory->expects(static::once())
            ->method('create')
            ->with($apiKey, $isTestMode)
            ->willReturn($expectedClient);

        static::assertEquals($expectedClient, $this->factory->create($apruveConfig));
    }

    /**
     * @return array
     */
    public function createDataProvider()
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

    /**
     * @return ApruveConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getApruveConfigMock()
    {
        return $this->createMock(ApruveConfigInterface::class);
    }
}
