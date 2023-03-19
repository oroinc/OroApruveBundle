<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Invoice\ApruveInvoiceBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;

class ApruveInvoiceBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Mandatory
     */
    private const AMOUNT_CENTS = 11130;
    private const CURRENCY = 'USD';
    private const LINE_ITEMS = [
        'sku1' => [
            'sku' => 'sku1',
            'quantity' => 100,
            'currency' => 'USD',
            'amount_cents' => 2000,
        ],
        'sku2' => [
            'sku' => 'sku2',
            'quantity' => 50,
            'currency' => 'USD',
            'amount_cents' => 1000,
        ],
    ];

    /**
     * Optional
     */
    private const SHIPPING_AMOUNT_CENTS = 1010;
    private const TAX_AMOUNT_CENTS = 110;
    private const ISSUE_ON_CREATE = true;
    private const MERCHANT_INVOICE_ID = '123';
    private const MERCHANT_NOTES = 'Sample merchant notes';
    private const DUE_AT_STRING = '2027-04-15T10:12:27-05:00';

    private ApruveInvoiceBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new ApruveInvoiceBuilder(
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::LINE_ITEMS
        );
    }

    public function testGetResult()
    {
        $actual = $this->builder->getResult();

        $expected = [
            ApruveInvoice::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveInvoice::CURRENCY => self::CURRENCY,
            ApruveInvoice::LINE_ITEMS => self::LINE_ITEMS,
        ];
        self::assertEquals($expected, $actual->getData());
    }

    public function testGetResultWithOptionalParams()
    {
        $this->builder->setIssueOnCreate(self::ISSUE_ON_CREATE);
        $this->builder->setDueAt(self::DUE_AT_STRING);
        $this->builder->setMerchantInvoiceId(self::MERCHANT_INVOICE_ID);
        $this->builder->setShippingCents(self::SHIPPING_AMOUNT_CENTS);
        $this->builder->setTaxCents(self::TAX_AMOUNT_CENTS);
        $this->builder->setMerchantNotes(self::MERCHANT_NOTES);

        $actual = $this->builder->getResult();

        $expected = [
            ApruveInvoice::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveInvoice::CURRENCY => self::CURRENCY,
            ApruveInvoice::LINE_ITEMS => self::LINE_ITEMS,
            ApruveInvoice::MERCHANT_INVOICE_ID => self::MERCHANT_INVOICE_ID,
            ApruveInvoice::SHIPPING_CENTS => self::SHIPPING_AMOUNT_CENTS,
            ApruveInvoice::TAX_CENTS => self::TAX_AMOUNT_CENTS,
            ApruveInvoice::ISSUE_ON_CREATE => self::ISSUE_ON_CREATE,
            ApruveInvoice::DUE_AT => self::DUE_AT_STRING,
            ApruveInvoice::MERCHANT_NOTES => self::MERCHANT_NOTES,
        ];
        self::assertEquals($expected, $actual->getData());
    }
}
