<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

class ApruveLineItem extends AbstractApruveEntity
{
    /**
     * Mandatory
     */
    public const TITLE = 'title';
    /**
     * Property 'price_total_cents' is not respected by Apruve when secure hash is generated,
     * hence we use 'amount_cents' instead.
     * @see README.md, section "Things to Consider"
     */
    public const AMOUNT_CENTS = 'amount_cents';
    public const PRICE_TOTAL_CENTS = 'price_total_cents';
    public const QUANTITY = 'quantity';
    public const CURRENCY = 'currency';

    /**
     * Optional
     */
    public const SKU = 'sku';
    public const DESCRIPTION = 'description';
    public const VIEW_PRODUCT_URL = 'view_product_url';
    public const PRICE_EA_CENTS = 'price_ea_cents';
    public const VENDOR = 'vendor';
    public const MERCHANT_NOTES = 'merchant_notes';
    public const VARIANT_INFO = 'variant_info';
}
