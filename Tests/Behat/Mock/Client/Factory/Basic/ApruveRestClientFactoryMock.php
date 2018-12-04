<?php

namespace Oro\Bundle\ApruveBundle\Tests\Behat\Mock\Client\Factory\Basic;

use Oro\Bundle\ApruveBundle\Client\Factory\ApruveRestClientFactoryInterface;
use Oro\Bundle\ApruveBundle\Tests\Behat\Mock\Client\ApruveRestClientMock;

class ApruveRestClientFactoryMock implements ApruveRestClientFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($apiKey, $isTestMode)
    {
        return new ApruveRestClientMock();
    }
}
