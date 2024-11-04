<?php

namespace Oro\Bundle\ApruveBundle\Method;

use Oro\Bundle\ApruveBundle\Apruve\Provider\SupportedCurrenciesProviderInterface;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\Executor\PaymentActionExecutor;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

class ApruvePaymentMethod implements PaymentMethodInterface
{
    const SHIPMENT = 'shipment';

    const PARAM_ORDER_ID = 'apruveOrderId';

    /**
     * @var ApruveConfigInterface
     */
    private $config;

    /**
     * @var PaymentActionExecutor
     */
    private $paymentActionExecutor;

    /**
     * @var SupportedCurrenciesProviderInterface
     */
    protected $supportedCurrenciesProvider;

    public function __construct(
        ApruveConfigInterface $config,
        SupportedCurrenciesProviderInterface $supportedCurrenciesProvider,
        PaymentActionExecutor $paymentActionExecutor
    ) {
        $this->config = $config;
        $this->paymentActionExecutor = $paymentActionExecutor;
        $this->supportedCurrenciesProvider = $supportedCurrenciesProvider;
    }

    /**
     * @throws \InvalidArgumentException
     */
    #[\Override]
    public function execute($action, PaymentTransaction $paymentTransaction)
    {
        return $this->paymentActionExecutor->execute($action, $this->config, $paymentTransaction);
    }

    #[\Override]
    public function getIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }

    #[\Override]
    public function isApplicable(PaymentContextInterface $context)
    {
        return $this->supportedCurrenciesProvider->isSupported($context->getCurrency());
    }

    #[\Override]
    public function supports($actionName)
    {
        return $this->paymentActionExecutor->supports($actionName);
    }
}
