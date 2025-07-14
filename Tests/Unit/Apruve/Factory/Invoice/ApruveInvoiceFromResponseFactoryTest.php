<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Factory\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Factory\Invoice\ApruveInvoiceFromResponseFactory;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApruveInvoiceFromResponseFactoryTest extends TestCase
{
    private const APRUVE_INVOICE = [
        'amount_cents' => 1000,
        'currency' => 'USD',
        'invoice_items' => [
            [
                'title' => 'Sample title',
                'amount_cents' => 1000,
                'currency' => 'USD',
                'quantity' => 1,
            ]
        ],
    ];

    private RestResponseInterface&MockObject $restResponse;
    private ApruveInvoiceFromResponseFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->restResponse = $this->createMock(RestResponseInterface::class);

        $this->factory = new ApruveInvoiceFromResponseFactory();
    }

    public function testCreateFromResponse(): void
    {
        $this->restResponse->expects(self::once())
            ->method('json')
            ->willReturn(self::APRUVE_INVOICE);

        $actual = $this->factory->createFromResponse($this->restResponse);

        self::assertEquals(self::APRUVE_INVOICE, $actual->getData());
    }
}
