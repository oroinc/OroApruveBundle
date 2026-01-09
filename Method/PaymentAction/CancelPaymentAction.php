<?php

namespace Oro\Bundle\ApruveBundle\Method\PaymentAction;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Handles payment cancellation action for Apruve.
 */
class CancelPaymentAction extends AbstractPaymentAction
{
    public const NAME = 'cancel';

    #[\Override]
    public function execute(ApruveConfigInterface $apruveConfig, PaymentTransaction $paymentTransaction)
    {
        // Stub for cancel action.
        // Not implemented (see BB-8127)
        $paymentTransaction->setSuccessful(false);

        return [];
    }

    #[\Override]
    public function getName()
    {
        return static::NAME;
    }
}
