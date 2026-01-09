<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment;

/**
 * Creates Apruve shipment builder instances.
 */
class ApruveShipmentBuilderFactory implements ApruveShipmentBuilderFactoryInterface
{
    #[\Override]
    public function create($amountCents, $currency, $shippedAt)
    {
        return new ApruveShipmentBuilder($amountCents, $currency, $shippedAt);
    }
}
