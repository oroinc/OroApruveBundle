<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Apruve\Builder\Shipment;

use Oro\Bundle\ApruveBundle\Apruve\Builder\Shipment\ApruveShipmentBuilder;
use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveShipment;
use PHPUnit\Framework\TestCase;

class ApruveShipmentBuilderTest extends TestCase
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
    private const SHIPPER = 'Sample Shipper Name';
    private const TRACKING_NUMBER = 'sampleTrackingNumber';
    private const STATUS = 'sampleStatus';
    private const MERCHANT_SHIPMENT_ID = '123';
    private const MERCHANT_NOTES = 'Sample merchant notes';
    private const SHIPPED_AT_STRING = '2027-04-15T10:12:27-05:00';
    private const DELIVERED_AT_STRING = '2027-04-15T10:12:27-05:00';

    private ApruveShipmentBuilder $builder;

    #[\Override]
    protected function setUp(): void
    {
        $this->builder = new ApruveShipmentBuilder(
            self::AMOUNT_CENTS,
            self::CURRENCY,
            self::SHIPPED_AT_STRING
        );
    }

    public function testGetResult(): void
    {
        $actual = $this->builder->getResult();

        $expected = [
            ApruveShipment::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveShipment::CURRENCY => self::CURRENCY,
            ApruveShipment::SHIPPED_AT => self::SHIPPED_AT_STRING,
        ];
        self::assertEquals($expected, $actual->getData());
    }

    public function testGetResultWithOptionalParams(): void
    {
        $this->builder->setLineItems(self::LINE_ITEMS);
        $this->builder->setTaxCents(self::TAX_AMOUNT_CENTS);
        $this->builder->setShippingCents(self::SHIPPING_AMOUNT_CENTS);
        $this->builder->setShipper(self::SHIPPER);
        $this->builder->setTrackingNumber(self::TRACKING_NUMBER);
        $this->builder->setDeliveredAt(self::DELIVERED_AT_STRING);
        $this->builder->setStatus(self::STATUS);
        $this->builder->setMerchantShipmentId(self::MERCHANT_SHIPMENT_ID);
        $this->builder->setMerchantNotes(self::MERCHANT_NOTES);

        $actual = $this->builder->getResult();

        $expected = [
            ApruveShipment::AMOUNT_CENTS => self::AMOUNT_CENTS,
            ApruveShipment::CURRENCY => self::CURRENCY,
            ApruveShipment::LINE_ITEMS => self::LINE_ITEMS,
            ApruveShipment::TAX_CENTS => self::TAX_AMOUNT_CENTS,
            ApruveShipment::SHIPPING_CENTS => self::SHIPPING_AMOUNT_CENTS,
            ApruveShipment::SHIPPER => self::SHIPPER,
            ApruveShipment::TRACKING_NUMBER => self::TRACKING_NUMBER,
            ApruveShipment::SHIPPED_AT => self::SHIPPED_AT_STRING,
            ApruveShipment::DELIVERED_AT => self::DELIVERED_AT_STRING,
            ApruveShipment::STATUS => self::STATUS,
            ApruveShipment::MERCHANT_SHIPMENT_ID => self::MERCHANT_SHIPMENT_ID,
            ApruveShipment::MERCHANT_NOTES => self::MERCHANT_NOTES,
        ];
        self::assertEquals($expected, $actual->getData());
    }
}
