<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Helper;

use Oro\Bundle\ApruveBundle\Apruve\Provider\SupportedCurrenciesProvider;

class SupportedCurrenciesProviderTest extends \PHPUnit\Framework\TestCase
{
    private SupportedCurrenciesProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new SupportedCurrenciesProvider();
    }

    public function testGetCurrencies()
    {
        $actual = $this->provider->getCurrencies();
        self::assertSame(['USD'], $actual);
    }

    /**
     * @dataProvider currenciesDataProvider
     */
    public function testIsSupported(string $currency, bool $expected)
    {
        self::assertSame($expected, $this->provider->isSupported($currency));
    }

    public function currenciesDataProvider(): array
    {
        return [
            ['USD', true],
            ['EUR', false],
        ];
    }
}
