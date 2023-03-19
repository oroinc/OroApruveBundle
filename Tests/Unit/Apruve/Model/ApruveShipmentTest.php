<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Model;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveShipment;

class ApruveShipmentTest extends \PHPUnit\Framework\TestCase
{
    private const ID = 'sampleId';
    private const DATA = [
        'id' => self::ID,
        'amount_cents' => 1000,
    ];

    private ApruveShipment $apruveShipment;

    protected function setUp(): void
    {
        $this->apruveShipment = new ApruveShipment(self::DATA);
    }

    public function testGetData()
    {
        self::assertSame(self::DATA, $this->apruveShipment->getData());
    }

    public function testGetId()
    {
        self::assertSame(self::ID, $this->apruveShipment->getId());
    }
}
