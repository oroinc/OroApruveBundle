<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment;

/**
 * Defines the contract for creating {@see ApruveShipmentBuilderInterface} instances
 * with the required shipment parameters.
 */
interface ApruveShipmentBuilderFactoryInterface
{
    /**
     * @param int    $amountCents
     * @param string $currency
     * @param string $shippedAt The ISO8601 date that the shipment was sent.
     *
     * @return ApruveShipmentBuilderInterface
     */
    public function create($amountCents, $currency, $shippedAt);
}
