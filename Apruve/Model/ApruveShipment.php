<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

class ApruveShipment extends AbstractApruveEntity
{
    /**
     * Mandatory
     */
    public const AMOUNT_CENTS = 'amount_cents';
    public const CURRENCY = 'currency';
    public const LINE_ITEMS = 'shipment_items';

    /**
     * Optional
     */
    public const TAX_CENTS = 'tax_cents';
    public const SHIPPING_CENTS = 'shipping_cents';
    public const SHIPPER = 'shipper';
    public const TRACKING_NUMBER = 'tracking_number';
    public const SHIPPED_AT = 'shipped_at';
    public const DELIVERED_AT = 'delivered_at';
    public const STATUS = 'status';
    public const MERCHANT_SHIPMENT_ID = 'merchant_shipment_id';
    public const MERCHANT_NOTES = 'merchant_notes';
}
