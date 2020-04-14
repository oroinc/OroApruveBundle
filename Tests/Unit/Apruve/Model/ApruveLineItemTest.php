<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Model;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;

class ApruveLineItemTest extends \PHPUnit\Framework\TestCase
{
    const ID = 'sampleId';
    const DATA = [
        'id' => self::ID,
        'merchantId' => 'sampleId',
    ];

    /**
     * @var ApruveLineItem
     */
    private $apruveLineItem;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->apruveLineItem = new ApruveLineItem(self::DATA);
    }

    public function testGetData()
    {
        static::assertSame(self::DATA, $this->apruveLineItem->getData());
    }

    public function testGetId()
    {
        static::assertSame(self::ID, $this->apruveLineItem->getId());
    }
}
