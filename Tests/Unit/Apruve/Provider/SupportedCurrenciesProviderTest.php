<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Provider;

use Oro\Bundle\ApruveBundle\Apruve\Provider\SupportedCurrenciesProvider;
use PHPUnit\Framework\TestCase;

class SupportedCurrenciesProviderTest extends TestCase
{
    private SupportedCurrenciesProvider $provider;

    #[\Override]
    protected function setUp(): void
    {
        $this->provider = new SupportedCurrenciesProvider();
    }

    public function testGetCurrencies(): void
    {
        $actual = $this->provider->getCurrencies();
        self::assertSame(['USD'], $actual);
    }

    /**
     * @dataProvider currenciesDataProvider
     */
    public function testIsSupported(string $currency, bool $expected): void
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
