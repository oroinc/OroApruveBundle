<?php

namespace Oro\Bundle\ApruveBundle\Method\PaymentAction;

use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

class CapturePaymentAction extends AbstractPaymentAction
{
    public const NAME = PaymentMethodInterface::CAPTURE;

    #[\Override]
    public function execute(ApruveConfigInterface $apruveConfig, PaymentTransaction $paymentTransaction)
    {
        $sourcePaymentTransaction = $paymentTransaction->getSourcePaymentTransaction();

        if ($sourcePaymentTransaction === null) {
            throw new \LogicException(
                'Capture payment transaction for Apruve should have source transaction with Invoice action'
            );
        }

        $paymentTransaction
            ->setReference($sourcePaymentTransaction->getReference())
            ->setActive(false)
            ->setSuccessful(true);

        return [];
    }

    #[\Override]
    public function getName()
    {
        return static::NAME;
    }
}
