<?php

namespace Oro\Bundle\ApruveBundle\Method\View\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

/**
 * Defines the contract for creating Apruve payment method view instances.
 */
interface ApruvePaymentMethodViewFactoryInterface
{
    /**
     * @param ApruveConfigInterface $config
     *
     * @return PaymentMethodViewInterface
     */
    public function create(ApruveConfigInterface $config);
}
