<?php

namespace Oro\Bundle\ApruveBundle\Method\Factory;

use Oro\Bundle\ApruveBundle\Apruve\Provider\SupportedCurrenciesProviderInterface;
use Oro\Bundle\ApruveBundle\Method\ApruvePaymentMethod;
use Oro\Bundle\ApruveBundle\Method\Config\ApruveConfigInterface;
use Oro\Bundle\ApruveBundle\Method\PaymentAction\Executor\PaymentActionExecutor;

/**
 * Creates Apruve payment method instances from configuration.
 */
class ApruvePaymentMethodFactory implements ApruvePaymentMethodFactoryInterface
{
    /**
     * @var PaymentActionExecutor
     */
    private $paymentActionExecutor;

    /**
     * @var SupportedCurrenciesProviderInterface
     */
    private $supportedCurrenciesProvider;

    public function __construct(
        PaymentActionExecutor $paymentActionExecutor,
        SupportedCurrenciesProviderInterface $supportedCurrenciesProvider
    ) {
        $this->paymentActionExecutor = $paymentActionExecutor;
        $this->supportedCurrenciesProvider = $supportedCurrenciesProvider;
    }

    #[\Override]
    public function create(ApruveConfigInterface $config)
    {
        return new ApruvePaymentMethod($config, $this->supportedCurrenciesProvider, $this->paymentActionExecutor);
    }
}
