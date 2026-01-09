<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem;

/**
 * Creates {@see ApruveLineItemBuilder} instances with the provided line item parameters.
 */
class ApruveLineItemBuilderFactory implements ApruveLineItemBuilderFactoryInterface
{
    #[\Override]
    public function create($title, $amountCents, $quantity, $currency)
    {
        return new ApruveLineItemBuilder($title, $amountCents, $quantity, $currency);
    }
}
