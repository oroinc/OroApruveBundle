<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Model;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveOrder;

class ApruveOrderTest extends \PHPUnit\Framework\TestCase
{
    private const ID = 'sampleId';
    private const DATA = [
        'id' => self::ID,
        'merchantId' => 'sampleId',
    ];

    private ApruveOrder $apruveOrder;

    #[\Override]
    protected function setUp(): void
    {
        $this->apruveOrder = new ApruveOrder(self::DATA);
    }

    public function testGetData()
    {
        self::assertSame(self::DATA, $this->apruveOrder->getData());
    }

    public function testGetId()
    {
        self::assertSame(self::ID, $this->apruveOrder->getId());
    }
}
