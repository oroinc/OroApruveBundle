<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Request\Merchant\Factory;

use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequest;
use Oro\Bundle\ApruveBundle\Client\Request\Merchant\Factory\BasicGetMerchantRequestFactory;
use PHPUnit\Framework\TestCase;

class BasicGetMerchantRequestFactoryTest extends TestCase
{
    private BasicGetMerchantRequestFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = new BasicGetMerchantRequestFactory();
    }

    public function testCreate(): void
    {
        $merchantId = '2124';

        $request = new ApruveRequest('GET', '/merchants/2124');

        $actual = $this->factory->createByMerchantId($merchantId);

        self::assertEquals($request, $actual);
    }
}
