<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Request\Invoice;

use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequest;
use Oro\Bundle\ApruveBundle\Client\Request\Merchant\Factory\BasicGetMerchantRequestFactory;

class BasicGetMerchantRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    private BasicGetMerchantRequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new BasicGetMerchantRequestFactory();
    }

    public function testCreate()
    {
        $merchantId = '2124';

        $request = new ApruveRequest('GET', '/merchants/2124');

        $actual = $this->factory->createByMerchantId($merchantId);

        self::assertEquals($request, $actual);
    }
}
