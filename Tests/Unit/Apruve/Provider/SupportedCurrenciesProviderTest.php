<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Helper;

use Oro\Bundle\ApruveBundle\Apruve\Provider\SupportedCurrenciesProvider;

class SupportedCurrenciesProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SupportedCurrenciesProvider
     */
    private $provider;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->provider = new SupportedCurrenciesProvider();
    }

    public function testGetCurrencies()
    {
        $actual = $this->provider->getCurrencies();
        static::assertSame(['USD'], $actual);
    }

    /**
     * @dataProvider currenciesDataProvider
     *
     * @param string $currency
     * @param string $expected
     */
    public function testIsSupported($currency, $expected)
    {
        $actual = $this->provider->isSupported($currency);
        static::assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function currenciesDataProvider()
    {
        return [
            ['USD', true],
            ['EUR', false],
        ];
    }
}
