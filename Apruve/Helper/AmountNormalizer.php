<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Helper;

class AmountNormalizer implements AmountNormalizerInterface
{
    #[\Override]
    public function normalize($amount)
    {
        $amountCents = ((float)$amount) * 100;

        return (int)round($amountCents, 0, PHP_ROUND_HALF_UP);
    }
}
