<?php

namespace Oro\Bundle\ApruveBundle\Method\View\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

interface ApruvePaymentMethodViewFactoryInterface
{
    /**
     * @param ApruveConfigInterface $config
     *
     * @return PaymentMethodViewInterface
     */
    public function create(ApruveConfigInterface $config);
}
