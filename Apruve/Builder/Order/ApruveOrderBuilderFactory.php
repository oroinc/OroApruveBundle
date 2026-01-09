<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Order;

/**
 * Creates {@see ApruveOrderBuilderFactoryInterface} instances with the provided order parameters.
 */
class ApruveOrderBuilderFactory implements ApruveOrderBuilderFactoryInterface
{
    #[\Override]
    public function create($merchantId, $amountCents, $currency, array $lineItems)
    {
        return new ApruveOrderBuilder($merchantId, $amountCents, $currency, $lineItems);
    }
}
