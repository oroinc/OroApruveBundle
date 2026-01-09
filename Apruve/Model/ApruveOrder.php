<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

/**
 * Represents an order in the Apruve payment system, defining constants for mandatory and optional order properties.
 */
class ApruveOrder extends AbstractApruveEntity
{
    /**
     * Mandatory
     */
    public const MERCHANT_ID = 'merchant_id';
    public const AMOUNT_CENTS = 'amount_cents';
    public const CURRENCY = 'currency';
    public const LINE_ITEMS = 'order_items';

    /**
     * Optional
     */
    public const MERCHANT_ORDER_ID = 'merchant_order_id';
    public const TAX_CENTS = 'tax_cents';
    public const SHIPPING_CENTS = 'shipping_cents';
    public const EXPIRE_AT = 'expire_at';
    public const AUTO_ESCALATE = 'auto_escalate';
    public const PO_NUMBER = 'po_number';
    public const PAYMENT_TERM_PARAMS = 'payment_term_params';
    public const _CORPORATE_ACCOUNT_ID = 'corporate_account_id';
    public const FINALIZE_ON_CREATE = 'finalize_on_create';
    public const INVOICE_ON_CREATE = 'invoice_on_create';

    /**
     * Required for offline (created manually via Apruve API) orders only.
     */
    public const SHOPPER_ID = 'shopper_id';
}
