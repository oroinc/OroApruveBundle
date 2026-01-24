<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice;

/**
 * Defines the contract for creating {@see ApruveInvoiceBuilderInterface} instances
 * with the required invoice parameters.
 */
interface ApruveInvoiceBuilderFactoryInterface
{
    /**
     * @param int    $amountCents
     * @param string $currency
     * @param array  $lineItems
     *
     * @return ApruveInvoiceBuilderInterface
     */
    public function create($amountCents, $currency, array $lineItems);
}
