<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Model;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;

class ApruveLineItemTest extends \PHPUnit\Framework\TestCase
{
    private const ID = 'sampleId';
    private const DATA = [
        'id' => self::ID,
        'merchantId' => 'sampleId',
    ];

    private ApruveLineItem $apruveLineItem;

    protected function setUp(): void
    {
        $this->apruveLineItem = new ApruveLineItem(self::DATA);
    }

    public function testGetData()
    {
        self::assertSame(self::DATA, $this->apruveLineItem->getData());
    }

    public function testGetId()
    {
        self::assertSame(self::ID, $this->apruveLineItem->getId());
    }
}
