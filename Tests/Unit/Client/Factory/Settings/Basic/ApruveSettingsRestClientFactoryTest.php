<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Factory\Settings\Basic;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\ApruveRestClientFactoryInterface;
use Oro\Bundle\ApruveBundle\Client\Factory\Settings\Basic\ApruveSettingsRestClientFactory;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;

class ApruveSettingsRestClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApruveRestClientFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $restClientFactory;

    /** @var SymmetricCrypterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $crypter;

    /** @var ApruveSettingsRestClientFactory */
    private $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->restClientFactory = $this->createMock(ApruveRestClientFactoryInterface::class);
        $this->crypter = $this->createMock(SymmetricCrypterInterface::class);

        $this->factory = new ApruveSettingsRestClientFactory($this->restClientFactory, $this->crypter);
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(bool $isTestMode)
    {
        $apruveSettings = $this->createMock(ApruveSettings::class);

        $encryptedKey = 'encrypted_api_key';

        $apruveSettings->expects(self::once())
            ->method('getApruveApiKey')
            ->willReturn($encryptedKey);

        $apruveSettings->expects(self::once())
            ->method('getApruveTestMode')
            ->willReturn($isTestMode);

        $apiKey = 'qwerty12345';

        $this->crypter->expects(self::once())
            ->method('decryptData')
            ->with($encryptedKey)
            ->willReturn($apiKey);

        $expectedClient = $this->createMock(ApruveRestClientInterface::class);

        $this->restClientFactory->expects(self::once())
            ->method('create')
            ->with($apiKey, $isTestMode)
            ->willReturn($expectedClient);

        self::assertEquals($expectedClient, $this->factory->create($apruveSettings));
    }

    public function createDataProvider(): array
    {
        return [
            'test mode' => [
                'isTestMode' => true,
            ],
            'prod mode' => [
                'isTestMode' => false,
            ],
        ];
    }
}
