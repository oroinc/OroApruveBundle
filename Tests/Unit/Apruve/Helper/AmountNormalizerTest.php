<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Helper;

use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizer;

class AmountNormalizerTest extends \PHPUnit\Framework\TestCase
{
    private AmountNormalizer $amountNormalizer;

    #[\Override]
    protected function setUp(): void
    {
        $this->amountNormalizer = new AmountNormalizer();
    }

    /**
     * @dataProvider normalizeDataProvider
     */
    public function testNormalize(mixed $amount, int $expected)
    {
        $actual = $this->amountNormalizer->normalize($amount);

        self::assertSame($expected, $actual);
    }

    public function normalizeDataProvider(): array
    {
        return [
            [10, 1000],
            [10.01, 1001],
            ['10', 1000],
            ['10.01', 1001],
            [null, 0],
            ['', 0],
            [false, 0],
        ];
    }
}
