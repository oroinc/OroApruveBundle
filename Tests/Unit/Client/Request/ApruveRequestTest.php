<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Request;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveEntityInterface;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequest;

class ApruveRequestTest extends \PHPUnit\Framework\TestCase
{
    private const METHOD = 'GET';
    private const URI = '/sampleUri';
    private const DATA = ['sampleData' => ['foo' => 'bar']];

    /** @var ApruveEntityInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $requestData;

    /** @var ApruveRequest */
    private $request;

    #[\Override]
    protected function setUp(): void
    {
        $this->requestData = $this->createMock(ApruveEntityInterface::class);
        $this->request = new ApruveRequest(self::METHOD, self::URI, $this->requestData);
    }

    public function testGetMethod()
    {
        $actual = $this->request->getMethod();
        self::assertSame(self::METHOD, $actual);
    }

    public function testGetUri()
    {
        $actual = $this->request->getUri();
        self::assertSame(self::URI, $actual);
    }

    public function testGetData()
    {
        $this->requestData->expects(self::any())
            ->method('getData')
            ->willReturn(self::DATA);
        $actual = $this->request->getData();

        self::assertSame(self::DATA, $actual);
    }

    public function testGetDataIfNoDataProvided()
    {
        $request = new ApruveRequest(self::METHOD, self::URI);
        $this->requestData->expects(self::any())
            ->method('getData')
            ->willReturn(self::DATA);
        $actual = $request->getData();

        self::assertSame([], $actual);
    }
}
