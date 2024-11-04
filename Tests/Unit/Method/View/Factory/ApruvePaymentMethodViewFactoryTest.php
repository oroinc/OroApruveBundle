<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\View\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\View\ApruvePaymentMethodView;
use Oro\Bundle\ApruveBundle\Method\View\Factory\ApruvePaymentMethodViewFactory;

class ApruvePaymentMethodViewFactoryTest extends \PHPUnit\Framework\TestCase
{
    private ApruvePaymentMethodViewFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = new ApruvePaymentMethodViewFactory();
    }

    public function testCreate()
    {
        $config = $this->createMock(ApruveConfigInterface::class);

        $method = new ApruvePaymentMethodView($config);

        self::assertEquals($method, $this->factory->create($config));
    }
}
