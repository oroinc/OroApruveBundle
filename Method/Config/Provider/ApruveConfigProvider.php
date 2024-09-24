<?php

namespace Oro\Bundle\ApruveBundle\Method\Config\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\Config\Factory\ApruveConfigFactoryInterface;
use Psr\Log\LoggerInterface;

class ApruveConfigProvider implements ApruveConfigProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var ApruveConfigFactoryInterface
     */
    protected $configFactory;

    /**
     * @var ApruveConfigInterface[]
     */
    protected $configs;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        ApruveConfigFactoryInterface $configFactory
    ) {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->configFactory = $configFactory;
    }

    #[\Override]
    public function getPaymentConfigs()
    {
        $settings = $this->getEnabledIntegrationSettings();

        $configs = [];
        foreach ($settings as $apruveSettings) {
            $config = $this->configFactory->create($apruveSettings);
            $configs[$config->getPaymentMethodIdentifier()] = $config;
        }

        return $configs;
    }

    #[\Override]
    public function getPaymentConfig($identifier)
    {
        $paymentConfigs = $this->getPaymentConfigs();

        if ([] === $paymentConfigs || false === array_key_exists($identifier, $paymentConfigs)) {
            return null;
        }

        return $paymentConfigs[$identifier];
    }

    #[\Override]
    public function hasPaymentConfig($identifier)
    {
        return null !== $this->getPaymentConfig($identifier);
    }

    /**
     * @return ApruveSettings[]
     */
    protected function getEnabledIntegrationSettings()
    {
        try {
            return $this->doctrine->getManagerForClass(ApruveSettings::class)
                ->getRepository(ApruveSettings::class)
                ->findEnabledSettings();
        } catch (\UnexpectedValueException $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }
}
