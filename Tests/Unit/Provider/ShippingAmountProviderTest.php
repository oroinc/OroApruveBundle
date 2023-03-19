<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit;

use Oro\Bundle\ApruveBundle\Provider\ShippingAmountProvider;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Model\Surcharge;
use Oro\Bundle\PaymentBundle\Provider\SurchargeProvider;

class ShippingAmountProviderTest extends \PHPUnit\Framework\TestCase
{
    private const AMOUNT = 10.0;

    /** @var PaymentContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentContext;

    /** @var ShippingAmountProvider */
    private $provider;

    protected function setUp(): void
    {
        $sourceEntity = $this->createMock(\stdClass::class);

        $this->paymentContext = $this->createMock(PaymentContextInterface::class);
        $this->paymentContext->expects(self::once())
            ->method('getSourceEntity')
            ->willReturn($sourceEntity);

        $surcharge = $this->createMock(Surcharge::class);
        $surcharge->expects(self::once())
            ->method('getShippingAmount')
            ->willReturn(self::AMOUNT);

        $surchargeProvider = $this->createMock(SurchargeProvider::class);
        $surchargeProvider->expects(self::once())
            ->method('getSurcharges')
            ->with($sourceEntity)
            ->willReturn($surcharge);

        $this->provider = new ShippingAmountProvider($surchargeProvider);
    }

    public function testGetShippingAmount()
    {
        $actual = $this->provider->getShippingAmount($this->paymentContext);

        self::assertSame(self::AMOUNT, $actual);
    }
}
