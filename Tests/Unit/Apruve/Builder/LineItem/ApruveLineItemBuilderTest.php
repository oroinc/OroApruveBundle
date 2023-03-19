<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Builder\LineItem;

use Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem\ApruveLineItemBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;

class ApruveLineItemBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Mandatory
     */
    private const TITLE = 'Sample name';
    private const AMOUNT_CENTS = 12345;
    private const CURRENCY = 'USD';
    private const QUANTITY = 10;

    /**
     * Optional
     */
    private const SKU = 'sku1';
    private const DESCRIPTION = 'Sample description';
    private const AMOUNT_EA_CENTS = 1235;
    private const VIEW_PRODUCT_URL = 'http://example.com/product/view/1';
    private const MERCHANT_NOTES = 'Sample note';
    private const VENDOR = 'Sample vendor name';
    private const VARIANT_INFO = 'Sample variant';

    private ApruveLineItemBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new ApruveLineItemBuilder(
            self::TITLE,
            self::AMOUNT_CENTS,
            self::QUANTITY,
            self::CURRENCY
        );
    }

    public function testGetResult()
    {
        $actual = $this->builder->getResult();

        $expected = [
            ApruveLineItem::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveLineItem::PRICE_TOTAL_CENTS => self::AMOUNT_CENTS,
            ApruveLineItem::QUANTITY => self::QUANTITY,
            ApruveLineItem::CURRENCY => self::CURRENCY,
            ApruveLineItem::TITLE => self::TITLE,
        ];

        self::assertEquals($expected, $actual->getData());
    }

    public function testGetResultWithOptionalParams()
    {
        $this->builder->setSku(self::SKU);
        $this->builder->setDescription(self::DESCRIPTION);
        $this->builder->setViewProductUrl(self::VIEW_PRODUCT_URL);
        $this->builder->setMerchantNotes(self::MERCHANT_NOTES);
        $this->builder->setVendor(self::VENDOR);
        $this->builder->setEaCents(self::AMOUNT_EA_CENTS);
        $this->builder->setVariantInfo(self::VARIANT_INFO);

        $actual = $this->builder->getResult();

        $expected = [
            // Mandatory params.
            ApruveLineItem::TITLE => self::TITLE,
            ApruveLineItem::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveLineItem::PRICE_TOTAL_CENTS => self::AMOUNT_CENTS,
            ApruveLineItem::CURRENCY => self::CURRENCY,
            ApruveLineItem::QUANTITY => self::QUANTITY,

            // Optional params.
            ApruveLineItem::SKU => self::SKU,
            ApruveLineItem::DESCRIPTION => self::DESCRIPTION,
            ApruveLineItem::VIEW_PRODUCT_URL => self::VIEW_PRODUCT_URL,
            ApruveLineItem::MERCHANT_NOTES => self::MERCHANT_NOTES,
            ApruveLineItem::VENDOR => self::VENDOR,
            ApruveLineItem::PRICE_EA_CENTS => self::AMOUNT_EA_CENTS,
            ApruveLineItem::VARIANT_INFO => self::VARIANT_INFO,
        ];

        self::assertEquals($expected, $actual->getData());
    }
}
