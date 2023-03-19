<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Url\Provider\Basic;

use Oro\Bundle\ApruveBundle\Client\Url\Provider\Basic\BasicApruveClientUrlProvider;

class BasicApruveClientUrlProviderTest extends \PHPUnit\Framework\TestCase
{
    private BasicApruveClientUrlProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new BasicApruveClientUrlProvider();
    }

    public function testGetTestModeUrl()
    {
        self::assertEquals('https://test.apruve.com/api/v4/', $this->provider->getApruveUrl(true));
    }

    public function testGetProdModeUrl()
    {
        self::assertEquals('https://app.apruve.com/api/v4/', $this->provider->getApruveUrl(false));
    }
}
