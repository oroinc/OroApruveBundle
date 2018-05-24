<?php

namespace Oro\Bundle\ApruveBundle\Tests\Behat\Mock\Method\View;

use Oro\Bundle\ApruveBundle\Method\View\ApruvePaymentMethodView;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;

class ApruvePaymentMethodViewMock extends ApruvePaymentMethodView
{
    /**
     * {@inheritdoc}
     */
    public function getOptions(PaymentContextInterface $context)
    {
        $options =  parent::getOptions($context);
        $options['componentOptions']['apruveJsUrls'] = [
            'test' => 'oroapruve/js/stubs/apruvejs-stub',
            'prod' => 'oroapruve/js/stubs/apruvejs-stub',
        ];
        return $options;
    }
}
