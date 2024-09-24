<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\View\Provider;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\Config\Provider\ApruveConfigProviderInterface;
use Oro\Bundle\ApruveBundle\Method\View\Factory\ApruvePaymentMethodViewFactoryInterface;
use Oro\Bundle\ApruveBundle\Method\View\Provider\ApruvePaymentMethodViewProvider;
use Oro\Bundle\PaymentBundle\Tests\Unit\Method\View\Provider\AbstractMethodViewProviderTest;

class ApruvePaymentMethodViewProviderTest extends AbstractMethodViewProviderTest
{
    /** @var ApruvePaymentMethodViewFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $factory;

    /** @var ApruveConfigProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $configProvider;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = $this->createMock(ApruvePaymentMethodViewFactoryInterface::class);
        $this->configProvider = $this->createMock(ApruveConfigProviderInterface::class);
        $this->paymentConfigClass = ApruveConfigInterface::class;
        $this->provider = new ApruvePaymentMethodViewProvider($this->configProvider, $this->factory);
    }
}
