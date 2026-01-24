<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Helper;

/**
 * Defines the contract for converting decimal amounts to integer cent values
 * required by the Apruve payment API.
 */
interface AmountNormalizerInterface
{
    /**
     * @param string|int|float $amount
     *
     * @return int
     */
    public function normalize($amount);
}
