<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice;

class ApruveInvoiceBuilderFactory implements ApruveInvoiceBuilderFactoryInterface
{
    #[\Override]
    public function create($amountCents, $currency, array $lineItems)
    {
        return new ApruveInvoiceBuilder($amountCents, $currency, $lineItems);
    }
}
