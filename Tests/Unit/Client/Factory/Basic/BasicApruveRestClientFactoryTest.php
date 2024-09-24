<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Factory\Basic;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClient;
use Oro\Bundle\ApruveBundle\Client\Factory\Basic\BasicApruveRestClientFactory;
use Oro\Bundle\ApruveBundle\Client\Url\Provider\ApruveClientUrlProviderInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientFactoryInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientInterface;

class BasicApruveRestClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    private const SAMPLE_URI = '/sample-uri';
    private const SAMPLE_API_KEY = 'qwerty12345';

    /** @var ApruveClientUrlProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlProvider;

    /** @var RestClientFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $integrationRestClientFactory;

    /** @var BasicApruveRestClientFactory */
    private $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->urlProvider = $this->createMock(ApruveClientUrlProviderInterface::class);
        $this->integrationRestClientFactory = $this->createMock(RestClientFactoryInterface::class);

        $this->factory = new BasicApruveRestClientFactory($this->urlProvider, $this->integrationRestClientFactory);
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(bool $isTestMode)
    {
        $apiKey = self::SAMPLE_API_KEY;
        $uri = self::SAMPLE_URI;
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Apruve-Api-Key' => $apiKey,
            ]
        ];

        $this->urlProvider->expects(self::once())
            ->method('getApruveUrl')
            ->with($isTestMode)
            ->willReturn($uri);

        $integrationRestClient = $this->createMock(RestClientInterface::class);

        $this->integrationRestClientFactory->expects(self::once())
            ->method('createRestClient')
            ->with($uri, $options)
            ->willReturn($integrationRestClient);

        $expected = new ApruveRestClient($integrationRestClient);

        $actual = $this->factory->create($apiKey, $isTestMode);

        self::assertEquals($expected, $actual);
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
