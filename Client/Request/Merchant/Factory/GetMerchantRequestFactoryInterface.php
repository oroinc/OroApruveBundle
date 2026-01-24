<?php

namespace Oro\Bundle\ApruveBundle\Client\Request\Merchant\Factory;

use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;

/**
 * Defines the contract for creating merchant requests.
 */
interface GetMerchantRequestFactoryInterface
{
    /**
     * @param string $merchantId
     *
     * @return ApruveRequestInterface
     */
    public function createByMerchantId($merchantId);
}
