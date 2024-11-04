<?php

namespace Oro\Bundle\ApruveBundle\Method\View\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\View\ApruvePaymentMethodView;

class ApruvePaymentMethodViewFactory implements ApruvePaymentMethodViewFactoryInterface
{
    #[\Override]
    public function create(ApruveConfigInterface $config)
    {
        return new ApruvePaymentMethodView($config);
    }
}
