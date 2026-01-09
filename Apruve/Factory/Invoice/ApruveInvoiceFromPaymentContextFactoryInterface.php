<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Factory\Invoice;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveInvoice;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;

/**
 * Defines the contract for creating {@see ApruveInvoice} instances
 * from {@see PaymentContextInterface} data.
 */
interface ApruveInvoiceFromPaymentContextFactoryInterface
{
    /**
     * @param PaymentContextInterface $paymentContext
     *
     * @return ApruveInvoice
     */
    public function createFromPaymentContext(PaymentContextInterface $paymentContext);
}
