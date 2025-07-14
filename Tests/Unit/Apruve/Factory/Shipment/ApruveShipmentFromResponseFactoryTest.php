<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Factory\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Factory\Shipment\ApruveShipmentFromResponseFactory;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApruveShipmentFromResponseFactoryTest extends TestCase
{
    private const APRUVE_SHIPMENT = [
        'amount_cents' => 1000,
        'currency' => 'USD',
        'shipment_items' => [
            [
                'title' => 'Sample title',
                'amount_cents' => 1000,
                'currency' => 'USD',
                'quantity' => 1,
            ]
        ],
    ];

    private RestResponseInterface&MockObject $restResponse;
    private ApruveShipmentFromResponseFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->restResponse = $this->createMock(RestResponseInterface::class);

        $this->factory = new ApruveShipmentFromResponseFactory();
    }

    public function testCreateFromResponse(): void
    {
        $this->restResponse->expects(self::once())
            ->method('json')
            ->willReturn(self::APRUVE_SHIPMENT);

        $actual = $this->factory->createFromResponse($this->restResponse);

        self::assertEquals(self::APRUVE_SHIPMENT, $actual->getData());
    }
}
