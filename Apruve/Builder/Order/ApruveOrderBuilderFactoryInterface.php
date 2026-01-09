<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Order;

/**
 * Defines the contract for creating {@see ApruveOrderBuilderInterface} instances
 * with the required order parameters.
 */
interface ApruveOrderBuilderFactoryInterface
{
    /**
     * @param string $merchantId
     * @param int    $amountCents
     * @param string $currency
     * @param array  $lineItems
     *
     * @return ApruveOrderBuilderInterface
     */
    public function create($merchantId, $amountCents, $currency, array $lineItems);
}
