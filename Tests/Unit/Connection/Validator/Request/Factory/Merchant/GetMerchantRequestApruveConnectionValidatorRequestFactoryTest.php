<?php

namespace Oro\Bundle\ApruveBundle\Connection\Validator\Request\Factory\Merchant;

use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\ApruveBundle\Client\Request\Merchant\Factory\GetMerchantRequestFactoryInterface;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;

class GetMerchantRequestApruveConnectionValidatorRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var GetMerchantRequestFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $merchantRequestFactory;

    /** @var SymmetricCrypterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $symmetricCrypter;

    /** @var GetMerchantRequestApruveConnectionValidatorRequestFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->merchantRequestFactory = $this->createMock(GetMerchantRequestFactoryInterface::class);
        $this->symmetricCrypter = $this->createMock(SymmetricCrypterInterface::class);

        $this->factory = new GetMerchantRequestApruveConnectionValidatorRequestFactory(
            $this->merchantRequestFactory,
            $this->symmetricCrypter
        );
    }

    public function testCreateBySettings()
    {
        $settings = $this->createMock(ApruveSettings::class);

        $encryptedMerchantId = 'encrypted_merchant_id';

        $settings->expects(self::once())
            ->method('getApruveMerchantId')
            ->willReturn($encryptedMerchantId);

        $merchantId = 'merchant_id';

        $this->symmetricCrypter->expects(self::once())
            ->method('decryptData')
            ->with($encryptedMerchantId)
            ->willReturn($merchantId);

        $request = $this->createMock(ApruveRequestInterface::class);

        $this->merchantRequestFactory->expects(self::once())
            ->method('createByMerchantId')
            ->with($merchantId)
            ->willReturn($request);

        self::assertEquals($request, $this->factory->createBySettings($settings));
    }
}
