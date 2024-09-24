<?php

namespace Oro\Bundle\ApruveBundle\Connection\Validator\Request\Factory\Merchant;

use Oro\Bundle\ApruveBundle\Client\Request\Merchant\Factory\GetMerchantRequestFactoryInterface;
use Oro\Bundle\ApruveBundle\Connection\Validator\Request\Factory\ApruveConnectionValidatorRequestFactoryInterface;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;

class GetMerchantRequestApruveConnectionValidatorRequestFactory implements
    ApruveConnectionValidatorRequestFactoryInterface
{
    /**
     * @var GetMerchantRequestFactoryInterface
     */
    private $merchantRequestFactory;

    /**
     * @var SymmetricCrypterInterface
     */
    private $symmetricCrypter;

    public function __construct(
        GetMerchantRequestFactoryInterface $merchantRequestFactory,
        SymmetricCrypterInterface $symmetricCrypter
    ) {
        $this->merchantRequestFactory = $merchantRequestFactory;
        $this->symmetricCrypter = $symmetricCrypter;
    }

    #[\Override]
    public function createBySettings(ApruveSettings $settings)
    {
        $merchantId = $this->symmetricCrypter->decryptData($settings->getApruveMerchantId());

        return $this->merchantRequestFactory->createByMerchantId($merchantId);
    }
}
