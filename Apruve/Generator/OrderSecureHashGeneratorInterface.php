<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Generator;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveOrder;

/**
 * Defines the contract for generating secure hashes used to validate
 * {@see ApruveOrder} data in Apruve API requests.
 */
interface OrderSecureHashGeneratorInterface
{
    /**
     * @param ApruveOrder $apruveOrder
     * @param string      $apiKey Merchant API key.
     *
     * @return string
     */
    public function generate(ApruveOrder $apruveOrder, $apiKey);
}
