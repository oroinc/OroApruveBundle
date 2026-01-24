<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Helper;

/**
 * Converts decimal amounts to integer cents for Apruve API.
 *
 * Normalizes monetary amounts by multiplying by 100 and rounding to the nearest integer.
 */
class AmountNormalizer implements AmountNormalizerInterface
{
    #[\Override]
    public function normalize($amount)
    {
        $amountCents = ((float)$amount) * 100;

        return (int)round($amountCents, 0, PHP_ROUND_HALF_UP);
    }
}
