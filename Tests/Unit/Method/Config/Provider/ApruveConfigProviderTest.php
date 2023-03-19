<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\Config\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Entity\Repository\ApruveSettingsRepository;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\Config\Factory\ApruveConfigFactoryInterface;
use Oro\Bundle\ApruveBundle\Method\Config\Provider\ApruveConfigProvider;
use Oro\Bundle\ApruveBundle\Method\Config\Provider\ApruveConfigProviderInterface;
use Psr\Log\LoggerInterface;

class ApruveConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    private const IDENTIFIER1 = 'apruve_1';
    private const IDENTIFIER2 = 'apruve_2';
    private const WRONG_IDENTIFIER = 'wrongpayment_method';

    /** @var ApruveSettingsRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $settingsRepository;

    /** @var array */
    private $configs;

    /** @var ApruveConfigProviderInterface */
    private $testedProvider;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    protected function setUp(): void
    {
        $this->settingsRepository = $this->createMock(ApruveSettingsRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $settingsOneMock = $this->createMock(ApruveSettings::class);
        $settingsTwoMock = $this->createMock(ApruveSettings::class);

        $configOneMock = $this->createMock(ApruveConfigInterface::class);
        $configTwoMock = $this->createMock(ApruveConfigInterface::class);

        $settingsMocks = [$settingsOneMock, $settingsTwoMock];

        $configOneMock->expects(self::any())
            ->method('getPaymentMethodIdentifier')
            ->willReturn(self::IDENTIFIER1);

        $configTwoMock->expects(self::any())
            ->method('getPaymentMethodIdentifier')
            ->willReturn(self::IDENTIFIER2);

        $this->settingsRepository->expects(self::once())
            ->method('findEnabledSettings')
            ->willReturn($settingsMocks);

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects(self::once())
            ->method('getRepository')
            ->willReturn($this->settingsRepository);

        $doctrine = $this->createMock(ManagerRegistry::class);
        $doctrine->expects(self::once())
            ->method('getManagerForClass')
            ->willReturn($objectManager);

        $configFactory = $this->createMock(ApruveConfigFactoryInterface::class);
        $configFactory->expects(self::any())
            ->method('create')
            ->willReturnMap([
                [$settingsOneMock, $configOneMock],
                [$settingsTwoMock, $configTwoMock]
            ]);

        $this->configs = [
            self::IDENTIFIER1 => $configOneMock,
            self::IDENTIFIER2 => $configTwoMock
        ];

        $this->testedProvider = new ApruveConfigProvider($doctrine, $this->logger, $configFactory);
    }

    public function testGetPaymentConfigs()
    {
        $actualResult = $this->testedProvider->getPaymentConfigs();

        self::assertSame($this->configs, $actualResult);
    }

    public function testGetPaymentConfig()
    {
        $expectedResult = $this->configs[self::IDENTIFIER1];
        $actualResult = $this->testedProvider->getPaymentConfig(self::IDENTIFIER1);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testGetPaymentConfigWhenNoSettings()
    {
        $this->settingsRepository->expects(self::once())
            ->method('findEnabledSettings')
            ->willReturn([]);

        $actualResult = $this->testedProvider->getPaymentConfig(self::WRONG_IDENTIFIER);

        self::assertNull($actualResult);
    }

    public function testHasPaymentConfig()
    {
        $expectedResult = true;
        $actualResult = $this->testedProvider->hasPaymentConfig(self::IDENTIFIER1);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testHasPaymentConfigWhenNoSettings()
    {
        $this->settingsRepository->expects(self::once())
            ->method('findEnabledSettings')
            ->willReturn([]);

        $actualResult = $this->testedProvider->hasPaymentConfig('somePaymentMethodId');

        self::assertFalse($actualResult);
    }

    public function testHasPaymentConfigWithException()
    {
        $this->settingsRepository->expects(self::once())
            ->method('findEnabledSettings')
            ->willThrowException(new \UnexpectedValueException());

        $this->logger->expects(self::once())
            ->method('error');

        $actualResult = $this->testedProvider->hasPaymentConfig('somePaymentMethodId');

        self::assertFalse($actualResult);
    }
}
