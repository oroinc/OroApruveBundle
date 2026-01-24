<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;

/**
 * Creates {@see ApruveInvoice} from API response ({@see RestResponseInterface}) data.
 */
class ApruveInvoiceFromResponseFactory implements ApruveInvoiceFromResponseFactoryInterface
{
    #[\Override]
    public function createFromResponse(RestResponseInterface $restResponse)
    {
        return new ApruveInvoice($restResponse->json());
    }
}
