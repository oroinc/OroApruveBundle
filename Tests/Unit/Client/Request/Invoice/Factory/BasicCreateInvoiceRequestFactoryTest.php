<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Client\Request\Invoice\Factory;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequest;
use Oro\Bundle\ApruveBundle\Client\Request\Invoice\Factory\BasicCreateInvoiceRequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BasicCreateInvoiceRequestFactoryTest extends TestCase
{
    private ApruveInvoice&MockObject $apruveInvoice;
    private BasicCreateInvoiceRequestFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->apruveInvoice = $this->createMock(ApruveInvoice::class);
        $this->factory = new BasicCreateInvoiceRequestFactory();
    }

    public function testCreate(): void
    {
        $apruveOrderId = '2124';
        $request = new ApruveRequest('POST', '/orders/2124/invoices', $this->apruveInvoice);

        $actual = $this->factory->create($this->apruveInvoice, $apruveOrderId);

        self::assertEquals($request, $actual);
    }
}
