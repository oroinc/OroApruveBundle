<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Order;

class ApruveOrderBuilderFactory implements ApruveOrderBuilderFactoryInterface
{
    #[\Override]
    public function create($merchantId, $amountCents, $currency, array $lineItems)
    {
        return new ApruveOrderBuilder($merchantId, $amountCents, $currency, $lineItems);
    }
}
