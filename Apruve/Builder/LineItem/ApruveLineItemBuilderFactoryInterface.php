<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem;

/**
 * Defines the contract for creating {@see ApruveLineItemBuilderInterface} instances
 * with the required line item parameters.
 */
interface ApruveLineItemBuilderFactoryInterface
{
    /**
     * @param string $title
     * @param int    $amountCents
     * @param int    $quantity
     * @param string $currency
     *
     * @return ApruveLineItemBuilderInterface
     */
    public function create($title, $amountCents, $quantity, $currency);
}
