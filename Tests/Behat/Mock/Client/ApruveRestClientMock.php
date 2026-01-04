<?php

namespace Oro\Bundle\ApruveBundle\Tests\Behat\Mock\Client;

use Oro\Bundle\ApruveBundle\Client\ApruveRestClientInterface;
use Oro\Bundle\ApruveBundle\Client\Request\ApruveRequestInterface;
use Oro\Bundle\IntegrationBundle\Test\FakeRestResponse;

class ApruveRestClientMock implements ApruveRestClientInterface
{
    public const TEST_ID = 'test_id';

    #[\Override]
    public function execute(ApruveRequestInterface $apruveRequest)
    {
        return new FakeRestResponse(200, [], json_encode(array_merge($apruveRequest->getData(), [
            'id' => self::TEST_ID
        ])));
    }
}
