<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Builder\LineItem;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;

/**
 * Builds {@see ApruveLineItem} instances with fluent interface
 * by accumulating optional properties through method chaining.
 */
class ApruveLineItemBuilder implements ApruveLineItemBuilderInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $amountCents;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $currency;

    /**
     * @param string $title
     * @param int    $amountCents
     * @param int    $quantity
     * @param string $currency
     */
    public function __construct($title, $amountCents, $quantity, $currency)
    {
        $this->title = $title;
        $this->amountCents = $amountCents;
        $this->quantity = $quantity;
        $this->currency = $currency;
    }

    #[\Override]
    public function getResult()
    {
        $this->data += [
            ApruveLineItem::TITLE => (string)$this->title,
            ApruveLineItem::AMOUNT_CENTS => (int)$this->amountCents,
            ApruveLineItem::PRICE_TOTAL_CENTS => (int)$this->amountCents,
            ApruveLineItem::QUANTITY => (int)$this->quantity,
            ApruveLineItem::CURRENCY => (string)$this->currency,
        ];

        return new ApruveLineItem($this->data);
    }

    #[\Override]
    public function setMerchantNotes($notes)
    {
        $this->data[ApruveLineItem::MERCHANT_NOTES] = (string)$notes;

        return $this;
    }

    #[\Override]
    public function setVendor($vendor)
    {
        $this->data[ApruveLineItem::VENDOR] = (string)$vendor;

        return $this;
    }

    #[\Override]
    public function setVariantInfo($info)
    {
        $this->data[ApruveLineItem::VARIANT_INFO] = (string)$info;

        return $this;
    }

    #[\Override]
    public function setSku($sku)
    {
        $this->data[ApruveLineItem::SKU] = (string)$sku;

        return $this;
    }

    #[\Override]
    public function setDescription($description)
    {
        $this->data[ApruveLineItem::DESCRIPTION] = (string)$description;

        return $this;
    }

    #[\Override]
    public function setViewProductUrl($url)
    {
        $this->data[ApruveLineItem::VIEW_PRODUCT_URL] = (string)$url;

        return $this;
    }

    #[\Override]
    public function setEaCents($amount)
    {
        $this->data[ApruveLineItem::PRICE_EA_CENTS] = (int)$amount;

        return $this;
    }
}
