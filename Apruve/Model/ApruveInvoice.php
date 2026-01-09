<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

/**
 * Represents an invoice in the Apruve payment system, defining constants for mandatory and optional invoice properties.
 */
class ApruveInvoice extends AbstractApruveEntity
{
    /**
     * Mandatory
     */
    public const AMOUNT_CENTS = 'amount_cents';
    public const CURRENCY = 'currency';
    public const LINE_ITEMS = 'invoice_items';

    /**
     * Optional
     */
    public const TAX_CENTS = 'tax_cents';
    public const SHIPPING_CENTS = 'shipping_cents';
    public const ISSUE_ON_CREATE = 'issue_on_create';
    public const DUE_AT = 'due_at';
    public const MERCHANT_INVOICE_ID = 'merchant_invoice_id';
    public const MERCHANT_NOTES = 'merchant_notes';
}
