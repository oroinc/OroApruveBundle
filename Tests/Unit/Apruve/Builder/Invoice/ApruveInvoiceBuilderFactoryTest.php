<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Builder\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice\ApruveInvoiceBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice\ApruveInvoiceBuilderFactory;
use PHPUnit\Framework\TestCase;

class ApruveInvoiceBuilderFactoryTest extends TestCase
{
    private const AMOUNT_CENTS = 11130;
    private const CURRENCY = 'USD';
    private const LINE_ITEMS = [
        'sku1' => [
            'sku' => 'sku1',
            'quantity' => 100,
            'currency' => 'USD',
            'amount_cents' => 2000,
        ],
        'sku2' => [
            'sku' => 'sku2',
            'quantity' => 50,
            'currency' => 'USD',
            'amount_cents' => 1000,
        ],
    ];

    private ApruveInvoiceBuilderFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = new ApruveInvoiceBuilderFactory();
    }

    public function testCreate(): void
    {
        $actual = $this->factory->create(
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::LINE_ITEMS
        );

        $expected = new ApruveInvoiceBuilder(
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::LINE_ITEMS
        );

        self::assertEquals($expected, $actual);
    }
}
