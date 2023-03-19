<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClient;
use Oro\Bundle\ApruveBundle\Client\Exception\UnsupportedMethodException;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;

class ApruveRestClientTest extends \PHPUnit\Framework\TestCase
{
    private const SAMPLE_URI = '/sample-uri';
    private const SAMPLE_DATA = ['sample_data' => 'foo'];

    /** @var RestClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $restClient;

    /** @var ApruveRestClient */
    private $client;

    protected function setUp(): void
    {
        $this->restClient = $this->createMock(RestClientInterface::class);

        $this->client = new ApruveRestClient($this->restClient);
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(string $method)
    {
        $response = $this->createMock(RestResponseInterface::class);

        $uri = self::SAMPLE_URI;
        $data = self::SAMPLE_DATA;

        $this->restClient->expects(self::once())
            ->method($method)
            ->with($uri, $data)
            ->willReturn($response);

        $request = $this->createRequest($method, $uri, $data);

        self::assertEquals($response, $this->client->execute($request));
    }

    public function executeDataProvider(): array
    {
        return [
            'GET' => [
                'method' => 'GET',
            ],
            'POST' => [
                'method' => 'POST',
            ],
            'PUT' => [
                'method' => 'PUT',
            ],
        ];
    }

    public function testDelete()
    {
        $response = $this->createMock(RestResponseInterface::class);

        $method = ApruveRestClient::METHOD_DELETE;
        $uri = self::SAMPLE_URI;

        $this->restClient->expects(self::once())
            ->method($method)
            ->with($uri)
            ->willReturn($response);

        $request = $this->createRequest($method, $uri, []);

        self::assertEquals($response, $this->client->execute($request));
    }

    public function testExecuteWithUnsupportedMethod()
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->expectExceptionMessage('Rest client does not support method "UNSUPPORTED"');
        $method = 'UNSUPPORTED';
        $request = $this->createRequest($method, self::SAMPLE_URI, self::SAMPLE_DATA);
        $this->client->execute($request);
    }

    public function testExecuteDoesNotCatchAnyException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Any exception');
        $method = ApruveRestClient::METHOD_GET;
        $uri = self::SAMPLE_URI;

        $this->restClient->expects(self::once())
            ->method($method)
            ->with($uri)
            ->willThrowException(new \Exception('Any exception'));

        $request = $this->createRequest($method, $uri, []);
        $this->client->execute($request);
    }

    private function createRequest(string $method, string $uri, array $data): ApruveRequestInterface
    {
        $request = $this->createMock(ApruveRequestInterface::class);
        $request->expects(self::any())
            ->method('getMethod')
            ->willReturn($method);
        $request->expects(self::any())
            ->method('getUri')
            ->willReturn($uri);
        $request->expects(self::any())
            ->method('getData')
            ->willReturn($data);

        return $request;
    }
}
