<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveShipment;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;

class ApruveShipmentFromResponseFactory implements ApruveShipmentFromResponseFactoryInterface
{
    #[\Override]
    public function createFromResponse(RestResponseInterface $restResponse)
    {
        return new ApruveShipment($restResponse->json());
    }
}
