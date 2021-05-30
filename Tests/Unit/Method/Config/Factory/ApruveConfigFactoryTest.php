<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\Config\Factory;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfig;
use Oro\Bundle\ApruveBundle\Method\Config\Factory\ApruveConfigFactory;
use Oro\Bundle\ApruveBundle\Method\Config\Factory\ApruveConfigFactoryInterface;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;
use Psr\Log\LoggerInterface;

class ApruveConfigFactoryTest extends \PHPUnit\Framework\TestCase
{
    private const CHANNEL_NAME = 'apruve';
    private const LABEL = 'Apruve';
    private const SHORT_LABEL = 'Apruve (short)';
    private const PAYMENT_METHOD_ID = 'apruve_1';
    private const API_KEY = '213a9079914f3b5163c6190f31444528';
    private const MERCHANT_ID = '7b97ea0172e18cbd4d3bf21e2b525b2d';
    private const API_KEY_DECRYPTED = 'apiKeyDecrypted';
    private const MERCHANT_ID_DECRYPTED = 'merchantIdDecrypted';
    private const TEST_MODE = false;

    /** @var Channel|\PHPUnit\Framework\MockObject\MockObject */
    private $channel;

    /** @var SymmetricCrypterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $crypter;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var LocalizationHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $localizationHelper;

    /** @var IntegrationIdentifierGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $integrationIdentifierGenerator;

    /** @var ApruveConfigFactoryInterface */
    private $factory;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->crypter = $this->createMock(SymmetricCrypterInterface::class);
        $this->localizationHelper = $this->createMock(LocalizationHelper::class);
        $this->integrationIdentifierGenerator = $this->createMock(IntegrationIdentifierGeneratorInterface::class);
        $this->channel = $this->createMock(Channel::class);

        $this->factory = new ApruveConfigFactory(
            $this->localizationHelper,
            $this->integrationIdentifierGenerator,
            $this->crypter,
            $this->logger
        );
    }

    public function testCreate()
    {
        $this->crypter->expects($this->exactly(2))
            ->method('decryptData')
            ->willReturnMap([
                [self::API_KEY, self::API_KEY_DECRYPTED],
                [self::MERCHANT_ID, self::MERCHANT_ID_DECRYPTED],
            ]);

        $apruveSettings = $this->createApruveSettingsMock();

        $expectedSettings = new ApruveConfig(
            [
                ApruveConfig::FIELD_ADMIN_LABEL => self::CHANNEL_NAME,
                ApruveConfig::FIELD_LABEL => self::LABEL,
                ApruveConfig::FIELD_SHORT_LABEL => self::SHORT_LABEL,
                ApruveConfig::FIELD_PAYMENT_METHOD_IDENTIFIER => self::PAYMENT_METHOD_ID,
                ApruveConfig::API_KEY_KEY => self::API_KEY_DECRYPTED,
                ApruveConfig::MERCHANT_ID_KEY => self::MERCHANT_ID_DECRYPTED,
                ApruveConfig::TEST_MODE_KEY => self::TEST_MODE,
            ]
        );

        $actualSettings = $this->factory->create($apruveSettings);

        self::assertEquals($expectedSettings, $actualSettings);
    }

    public function testCreateWithDecryptionFailure()
    {
        $this->crypter->expects($this->exactly(2))
            ->method('decryptData')
            ->willThrowException(new \Exception());

        $apruveSettings = $this->createApruveSettingsMock();

        $this->logger->expects($this->exactly(2))
            ->method('error');

        $expectedSettings = new ApruveConfig(
            [
                ApruveConfig::FIELD_ADMIN_LABEL => self::CHANNEL_NAME,
                ApruveConfig::FIELD_LABEL => self::LABEL,
                ApruveConfig::FIELD_SHORT_LABEL => self::SHORT_LABEL,
                ApruveConfig::FIELD_PAYMENT_METHOD_IDENTIFIER => self::PAYMENT_METHOD_ID,
                ApruveConfig::API_KEY_KEY => '',
                ApruveConfig::MERCHANT_ID_KEY => '',
                ApruveConfig::TEST_MODE_KEY => self::TEST_MODE,
            ]
        );

        $actualSettings = $this->factory->create($apruveSettings);

        self::assertEquals($expectedSettings, $actualSettings);
    }

    /**
     * @return ApruveSettings|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createApruveSettingsMock()
    {
        $labelsCollection = $this->createMock(Collection::class);
        $shortLabelsCollection = $this->createMock(Collection::class);

        $this->channel->expects(self::once())
            ->method('getName')
            ->willReturn(self::CHANNEL_NAME);

        $this->integrationIdentifierGenerator->expects(self::once())
            ->method('generateIdentifier')
            ->with($this->channel)
            ->willReturn(self::PAYMENT_METHOD_ID);

        $this->localizationHelper->expects(self::exactly(2))
            ->method('getLocalizedValue')
            ->withConsecutive([$labelsCollection], [$shortLabelsCollection])
            ->willReturnOnConsecutiveCalls(self::LABEL, self::SHORT_LABEL);

        $apruveSettings = $this->createMock(ApruveSettings::class);
        $apruveSettings->expects(self::once())
            ->method('getChannel')
            ->willReturn($this->channel);
        $apruveSettings->expects(self::once())
            ->method('getLabels')
            ->willReturn($labelsCollection);
        $apruveSettings->expects(self::once())
            ->method('getShortLabels')
            ->willReturn($shortLabelsCollection);
        $apruveSettings->expects(self::once())
            ->method('getApruveApiKey')
            ->willReturn(self::API_KEY);
        $apruveSettings->expects(self::once())
            ->method('getApruveMerchantId')
            ->willReturn(self::MERCHANT_ID);
        $apruveSettings->expects(self::once())
            ->method('getApruveTestMode')
            ->willReturn(self::TEST_MODE);

        return $apruveSettings;
    }
}
