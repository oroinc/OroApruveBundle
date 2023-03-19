<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\View;

use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfig;
use Oro\Bundle\ApruveBundle\Method\View\ApruvePaymentMethodView;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;

class ApruvePaymentMethodViewTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApruvePaymentMethodView */
    private $methodView;

    /** @var ApruveConfig|\PHPUnit\Framework\MockObject\MockObject */
    private $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(ApruveConfig::class);

        $this->methodView = new ApruvePaymentMethodView($this->config);
    }

    public function testGetOptions()
    {
        $context = $this->createMock(PaymentContextInterface::class);

        $this->config->expects($this->once())->method('isTestMode')->willReturn(true);

        $this->assertEquals(
            [
                'componentOptions' => [
                    'orderIdParamName' => ApruvePaymentMethod::PARAM_ORDER_ID,
                    'testMode' => true
                ],
            ],
            $this->methodView->getOptions($context)
        );
    }

    public function testGetBlock()
    {
        $this->assertEquals('_payment_methods_apruve_widget', $this->methodView->getBlock());
    }

    public function testGetLabel()
    {
        $label = 'label';

        $this->config->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);

        $this->assertEquals($label, $this->methodView->getLabel());
    }

    public function testShortGetLabel()
    {
        $label = 'short label';

        $this->config->expects(self::once())
            ->method('getShortLabel')
            ->willReturn($label);

        $this->assertEquals($label, $this->methodView->getShortLabel());
    }

    public function testGetAdminLabel()
    {
        $label = 'admin label';

        $this->config->expects(self::once())
            ->method('getAdminLabel')
            ->willReturn($label);

        $this->assertEquals($label, $this->methodView->getAdminLabel());
    }

    public function testGetPaymentMethodIdentifier()
    {
        $identifier = 'apruve_1';

        $this->config->expects(self::once())
            ->method('getPaymentMethodIdentifier')
            ->willReturn($identifier);

        $this->assertEquals($identifier, $this->methodView->getPaymentMethodIdentifier());
    }
}
