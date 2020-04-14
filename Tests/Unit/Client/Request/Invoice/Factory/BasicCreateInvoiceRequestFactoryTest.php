<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Request\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequest;
use Oro\Bundle\ApruveBundle\Client\Request\Invoice\Factory\BasicCreateInvoiceRequestFactory;

class BasicCreateInvoiceRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ApruveInvoice|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apruveInvoice;

    /**
     * @var BasicCreateInvoiceRequestFactory
     */
    private $factory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->apruveInvoice = $this->createMock(ApruveInvoice::class);
        $this->factory = new BasicCreateInvoiceRequestFactory();
    }

    public function testCreate()
    {
        $apruveOrderId = '2124';
        $request = new ApruveRequest('POST', '/orders/2124/invoices', $this->apruveInvoice);

        $actual = $this->factory->create($this->apruveInvoice, $apruveOrderId);

        static::assertEquals($request, $actual);
    }
}
