<?php

namespace Oro\Bundle\ApruveBundle\Method\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * Defines the contract for creating Apruve payment method instances.
 */
interface ApruvePaymentMethodFactoryInterface
{
    /**
     * @param ApruveConfigInterface $config
     *
     * @return PaymentMethodInterface
     */
    public function create(ApruveConfigInterface $config);
}
