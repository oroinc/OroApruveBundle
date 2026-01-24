<?php

namespace Oro\Bundle\ApruveBundle\Method\PaymentAction;

use Oro\Bundle\PaymentBundle\Context\Factory\TransactionPaymentContextFactoryInterface;

/**
 * Provides common functionality for Apruve payment actions.
 *
 * This abstract class serves as a base for implementing payment actions in the Apruve payment method.
 * It provides access to the payment context factory which is used to create transaction payment contexts
 * for payment operations. Subclasses implement specific payment actions such as authorize, capture, and refund.
 */
abstract class AbstractPaymentAction implements PaymentActionInterface
{
    /**
     * @var TransactionPaymentContextFactoryInterface
     */
    protected $paymentContextFactory;

    public function __construct(TransactionPaymentContextFactoryInterface $paymentContextFactory)
    {
        $this->paymentContextFactory = $paymentContextFactory;
    }
}
