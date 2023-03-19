<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Request\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveShipment;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequest;
use Oro\Bundle\ApruveBundle\Client\Request\Shipment\Factory\BasicCreateShipmentRequestFactory;

class BasicCreateShipmentRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApruveShipment|\PHPUnit\Framework\MockObject\MockObject */
    private $apruveShipment;

    /** @var BasicCreateShipmentRequestFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->apruveShipment = $this->createMock(ApruveShipment::class);
        $this->factory = new BasicCreateShipmentRequestFactory();
    }

    public function testCreate()
    {
        $apruveOrderId = '2124';
        $request = new ApruveRequest('POST', '/invoices/2124/shipments', $this->apruveShipment);

        $actual = $this->factory->create($this->apruveShipment, $apruveOrderId);

        self::assertEquals($request, $actual);
    }
}
