<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Model;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;

class ApruveInvoiceTest extends \PHPUnit\Framework\TestCase
{
    private const ID = 'sampleId';
    private const DATA = [
        'id' => self::ID,
        'amount_cents' => 1000,
    ];

    private ApruveInvoice $apruveInvoice;

    protected function setUp(): void
    {
        $this->apruveInvoice = new ApruveInvoice(self::DATA);
    }

    public function testGetData()
    {
        self::assertSame(self::DATA, $this->apruveInvoice->getData());
    }

    public function testGetId()
    {
        self::assertSame(self::ID, $this->apruveInvoice->getId());
    }
}
