<?php

namespace Oro\Bundle\ApruveBundle\Method\Provider;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\Config\Provider\ApruveConfigProviderInterface;
use Oro\Bundle\ApruveBundle\Method\Factory\ApruvePaymentMethodFactoryInterface;
use Oro\Bundle\PaymentBundle\Method\Provider\AbstractPaymentMethodProvider;

/**
 * Provides Apruve payment method instances.
 */
class ApruvePaymentMethodProvider extends AbstractPaymentMethodProvider
{
    /**
     * @var ApruvePaymentMethodFactoryInterface
     */
    private $factory;

    /**
     * @var ApruveConfigProviderInterface
     */
    private $configProvider;

    public function __construct(
        ApruveConfigProviderInterface $configProvider,
        ApruvePaymentMethodFactoryInterface $factory
    ) {
        parent::__construct();

        $this->configProvider = $configProvider;
        $this->factory = $factory;
    }

    #[\Override]
    protected function collectMethods()
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addPaymentMethod($config);
        }
    }

    protected function addPaymentMethod(ApruveConfigInterface $config)
    {
        $this->addMethod(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create($config)
        );
    }
}
