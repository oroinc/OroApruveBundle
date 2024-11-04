<?php

namespace Oro\Bundle\ApruveBundle\Method\Config\Factory;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfig;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;
use Psr\Log\LoggerInterface;

class ApruveConfigFactory implements ApruveConfigFactoryInterface
{
    /**
     * @var LocalizationHelper
     */
    private $localizationHelper;

    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    private $identifierGenerator;

    /**
     * @var SymmetricCrypterInterface
     */
    private $crypter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LocalizationHelper $localizationHelper,
        IntegrationIdentifierGeneratorInterface $identifierGenerator,
        SymmetricCrypterInterface $crypter,
        LoggerInterface $logger
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->identifierGenerator = $identifierGenerator;
        $this->crypter = $crypter;
        $this->logger = $logger;
    }

    #[\Override]
    public function create(ApruveSettings $settings)
    {
        $params = [];
        $channel = $settings->getChannel();

        $params[ApruveConfig::FIELD_PAYMENT_METHOD_IDENTIFIER] =
            $this->identifierGenerator->generateIdentifier($channel);

        $params[ApruveConfig::FIELD_ADMIN_LABEL] = $channel->getName();
        $params[ApruveConfig::FIELD_LABEL] = $this->getLocalizedValue($settings->getLabels());
        $params[ApruveConfig::FIELD_SHORT_LABEL] = $this->getLocalizedValue($settings->getShortLabels());

        $params[ApruveConfig::API_KEY_KEY] = $this->decryptData($settings->getApruveApiKey());
        $params[ApruveConfig::MERCHANT_ID_KEY] = $this->decryptData($settings->getApruveMerchantId());
        $params[ApruveConfig::TEST_MODE_KEY] = $settings->getApruveTestMode();

        return new ApruveConfig($params);
    }

    /**
     * @param Collection $values
     *
     * @return string
     */
    private function getLocalizedValue(Collection $values)
    {
        return (string)$this->localizationHelper->getLocalizedValue($values);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    private function decryptData($data)
    {
        try {
            return $this->crypter->decryptData($data);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            // Decryption failure, might be caused by invalid/malformed/not encrypted data.
            return '';
        }
    }
}
