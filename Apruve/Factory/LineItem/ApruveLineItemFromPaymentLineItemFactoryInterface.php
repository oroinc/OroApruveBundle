<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\LineItem;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveLineItem;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItem;
use Oro\Bundle\PaymentBundle\Context\PaymentLineItemInterface;

/**
 * Describes a factory to create {@see ApruveLineItem} from {@see PaymentLineItem}.
 */
interface ApruveLineItemFromPaymentLineItemFactoryInterface
{
    public function createFromPaymentLineItem(PaymentLineItemInterface $paymentLineItem);
}
