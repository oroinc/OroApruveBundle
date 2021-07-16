<?php

namespace Oro\Bundle\ApruveBundle\Method\View;

use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

class ApruvePaymentMethodView implements PaymentMethodViewInterface
{
    /**
     * @var ApruveConfigInterface
     */
    private $config;

    public function __construct(ApruveConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(PaymentContextInterface $context)
    {
        return [
            'componentOptions' => [
                'orderIdParamName' => ApruvePaymentMethod::PARAM_ORDER_ID,
                'testMode' => $this->config->isTestMode(),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getBlock()
    {
        return '_payment_methods_apruve_widget';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return $this->config->getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getShortLabel()
    {
        return $this->config->getShortLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getAdminLabel()
    {
        return $this->config->getAdminLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentMethodIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }
}
