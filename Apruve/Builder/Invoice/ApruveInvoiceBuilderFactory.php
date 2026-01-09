<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice;

/**
 * Creates {@see ApruveInvoiceBuilder} instances with the provided invoice parameters.
 */
class ApruveInvoiceBuilderFactory implements ApruveInvoiceBuilderFactoryInterface
{
    #[\Override]
    public function create($amountCents, $currency, array $lineItems)
    {
        return new ApruveInvoiceBuilder($amountCents, $currency, $lineItems);
    }
}
