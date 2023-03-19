<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Order;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Order\ApruveOrderBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveOrder;

class ApruveOrderBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Mandatory
     */
    private const MERCHANT_ID = 'sampleMerchantId';
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
    private const FINALIZE_ON_CREATE = true;
    private const MERCHANT_ORDER_ID = '123';
    private const INVOICE_ON_CREATE = true;
    private const SHOPPER_ID = 'sampleShopperId';
    private const CORPORATE_ACCOUNT_ID = 'sampleAccountId';
    private const PO_NUMBER = '69000';
    private const AUTO_ESCALATE = true;
    private const EXPIRE_AT_STRING = '2027-04-15T10:12:27-05:00';

    private ApruveOrderBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new ApruveOrderBuilder(
            self::MERCHANT_ID,
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::LINE_ITEMS
        );
    }

    public function testGetResult()
    {
        $actual = $this->builder->getResult();

        $expected = [
            ApruveOrder::MERCHANT_ID => self::MERCHANT_ID,
            ApruveOrder::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveOrder::CURRENCY => self::CURRENCY,
            ApruveOrder::LINE_ITEMS => self::LINE_ITEMS,
        ];
        self::assertEquals($expected, $actual->getData());
    }

    public function testGetResultWithOptionalParams()
    {
        $this->builder->setFinalizeOnCreate(self::FINALIZE_ON_CREATE);
        $this->builder->setInvoiceOnCreate(self::INVOICE_ON_CREATE);
        $this->builder->setShopperId(self::SHOPPER_ID);
        $this->builder->setCorporateAccountId(self::CORPORATE_ACCOUNT_ID);
        $this->builder->setPoNumber(self::PO_NUMBER);
        $this->builder->setAutoEscalate(self::AUTO_ESCALATE);
        $this->builder->setExpireAt(self::EXPIRE_AT_STRING);
        $this->builder->setMerchantOrderId(self::MERCHANT_ORDER_ID);
        $this->builder->setShippingCents(self::SHIPPING_AMOUNT_CENTS);
        $this->builder->setTaxCents(self::TAX_AMOUNT_CENTS);

        $actual = $this->builder->getResult();

        $expected = [
            ApruveOrder::MERCHANT_ID => self::MERCHANT_ID,
            ApruveOrder::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveOrder::CURRENCY => self::CURRENCY,
            ApruveOrder::LINE_ITEMS => self::LINE_ITEMS,
            ApruveOrder::MERCHANT_ORDER_ID => self::MERCHANT_ORDER_ID,
            ApruveOrder::SHOPPER_ID => self::SHOPPER_ID,
            ApruveOrder::SHIPPING_CENTS => self::SHIPPING_AMOUNT_CENTS,
            ApruveOrder::TAX_CENTS => self::TAX_AMOUNT_CENTS,
            ApruveOrder::FINALIZE_ON_CREATE => self::FINALIZE_ON_CREATE,
            ApruveOrder::INVOICE_ON_CREATE => self::INVOICE_ON_CREATE,
            ApruveOrder::PO_NUMBER => self::PO_NUMBER,
            ApruveOrder::AUTO_ESCALATE => self::AUTO_ESCALATE,
            ApruveOrder::EXPIRE_AT => self::EXPIRE_AT_STRING,
            ApruveOrder::PAYMENT_TERM_PARAMS => [
                ApruveOrder::_CORPORATE_ACCOUNT_ID => self::CORPORATE_ACCOUNT_ID,
            ],
        ];
        self::assertEquals($expected, $actual->getData());
    }
}
