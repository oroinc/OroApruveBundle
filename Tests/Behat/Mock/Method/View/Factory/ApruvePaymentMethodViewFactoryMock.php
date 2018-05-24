<?php

namespace Oro\Bundle\ApruveBundle\Tests\Behat\Mock\Method\View\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\View\Factory\ApruvePaymentMethodViewFactoryInterface;
use Oro\Bundle\ApruveBundle\Tests\Behat\Mock\Method\View\ApruvePaymentMethodViewMock;

class ApruvePaymentMethodViewFactoryMock implements ApruvePaymentMethodViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ApruveConfigInterface $config)
    {
        return new ApruvePaymentMethodViewMock($config);
    }
}
