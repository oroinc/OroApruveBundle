<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Method\View\Factory;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\View\ApruvePaymentMethodView;
use Oro\Bundle\ApruveBundle\Method\View\Factory\ApruvePaymentMethodViewFactory;
use Oro\Bundle\ApruveBundle\Method\View\Factory\ApruvePaymentMethodViewFactoryInterface;

class ApruvePaymentMethodViewFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ApruvePaymentMethodViewFactoryInterface
     */
    private $factory;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->factory = new ApruvePaymentMethodViewFactory();
    }

    public function testCreate()
    {
        /** @var ApruveConfigInterface|\PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->createMock(ApruveConfigInterface::class);

        $method = new ApruvePaymentMethodView($config);

        static::assertEquals($method, $this->factory->create($config));
    }
}
