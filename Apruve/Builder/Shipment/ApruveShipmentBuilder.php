<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveShipment;

/**
 * Builds {@see ApruveShipment} instances with fluent interface by accumulating optional properties
 * through method chaining.
 */
class ApruveShipmentBuilder implements ApruveShipmentBuilderInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var int
     */
    private $amountCents;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $shippedAt;

    /**
     * @param int    $amountCents
     * @param string $currency
     * @param string $shippedAt The ISO8601 date that the shipment was sent.
     */
    public function __construct($amountCents, $currency, $shippedAt)
    {
        $this->amountCents = $amountCents;
        $this->currency = $currency;
        $this->shippedAt = $shippedAt;
    }

    #[\Override]
    public function getResult()
    {
        $this->data += [
            ApruveShipment::AMOUNT_CENTS => (int)$this->amountCents,
            ApruveShipment::CURRENCY => (string)$this->currency,
            ApruveShipment::SHIPPED_AT => (string)$this->shippedAt,
        ];

        return new ApruveShipment($this->data);
    }

    #[\Override]
    public function setLineItems($lineItems)
    {
        $this->data[ApruveShipment::LINE_ITEMS] = (array)$lineItems;

        return $this;
    }

    #[\Override]
    public function setMerchantShipmentId($shipmentId)
    {
        $this->data[ApruveShipment::MERCHANT_SHIPMENT_ID] = (string)$shipmentId;

        return $this;
    }

    #[\Override]
    public function setShippingCents($amount)
    {
        $this->data[ApruveShipment::SHIPPING_CENTS] = (int)$amount;

        return $this;
    }

    #[\Override]
    public function setTaxCents($amount)
    {
        $this->data[ApruveShipment::TAX_CENTS] = (int)$amount;

        return $this;
    }

    #[\Override]
    public function setMerchantNotes($notes)
    {
        $this->data[ApruveShipment::MERCHANT_NOTES] = (string)$notes;

        return $this;
    }

    #[\Override]
    public function setShipper($shipper)
    {
        $this->data[ApruveShipment::SHIPPER] = (string)$shipper;

        return $this;
    }

    #[\Override]
    public function setTrackingNumber($trackingNumber)
    {
        $this->data[ApruveShipment::TRACKING_NUMBER] = (string)$trackingNumber;

        return $this;
    }

    #[\Override]
    public function setDeliveredAt($deliveredAt)
    {
        $this->data[ApruveShipment::DELIVERED_AT] = (string)$deliveredAt;

        return $this;
    }

    #[\Override]
    public function setStatus($status)
    {
        $this->data[ApruveShipment::STATUS] = (string)$status;

        return $this;
    }
}
