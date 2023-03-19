<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Builder\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment\ApruveShipmentBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment\ApruveShipmentBuilderFactory;

class ApruveShipmentBuilderFactoryTest extends \PHPUnit\Framework\TestCase
{
    private const AMOUNT_CENTS = 11130;
    private const CURRENCY = 'USD';
    private const SHIPPED_AT_STRING = '2027-04-15T10:12:27-05:00';

    private ApruveShipmentBuilderFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ApruveShipmentBuilderFactory();
    }

    public function testCreate()
    {
        $actual = $this->factory->create(
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::SHIPPED_AT_STRING
        );

        $expected = new ApruveShipmentBuilder(
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::SHIPPED_AT_STRING
        );

        self::assertEquals($expected, $actual);
    }
}
