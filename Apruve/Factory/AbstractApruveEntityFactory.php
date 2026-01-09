<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory;

use Oro\Bundle\ApruveBundle\Apruve\Helper\AmountNormalizerInterface;
use Oro\Bundle\CurrencyBundle\Entity\Price;

/**
 * Provides common functionality for Apruve entity factories.
 *
 * This abstract class serves as a base for factories that create Apruve API entities.
 * It provides utility methods for normalizing monetary amounts and prices using the configured amount normalizer.
 * Subclasses can use these methods to ensure consistent amount formatting when creating Apruve entities.
 */
abstract class AbstractApruveEntityFactory
{
    /**
     * @var AmountNormalizerInterface
     */
    protected $amountNormalizer;

    public function __construct(AmountNormalizerInterface $amountNormalizer)
    {
        $this->amountNormalizer = $amountNormalizer;
    }

    /**
     * @param float|int|string $amount
     *
     * @return int
     */
    protected function normalizeAmount($amount)
    {
        return $this->amountNormalizer->normalize($amount);
    }

    /**
     * @param Price $price
     *
     * @return int
     */
    protected function normalizePrice(Price $price)
    {
        return $this->normalizeAmount($price->getValue());
    }
}
