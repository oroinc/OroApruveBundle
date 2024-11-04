<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Builder\LineItem;

use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilderFactory;

class ApruveLineItemBuilderFactoryTest extends \PHPUnit\Framework\TestCase
{
    private const TITLE = 'Sample name';
    private const AMOUNT_CENTS = 12345;
    private const CURRENCY = 'USD';
    private const QUANTITY = 10;

    private ApruveLineItemBuilderFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = new ApruveLineItemBuilderFactory();
    }

    public function testCreate()
    {
        $actual = $this->factory->create(
            self::TITLE,
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::QUANTITY
        );
        $expected = new ApruveLineItemBuilder(
            self::TITLE,
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::QUANTITY
        );

        self::assertEquals($expected, $actual);
    }
}
