<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class ApruveSettingsControllerTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], static::generateBasicAuthHeader());
    }

    public function testGenerateTokenAction()
    {
        $this->ajaxRequest(
            'POST',
            $this->getUrl('oro_apruve_generate_token')
        );
        $response = $this->client->getResponse();
        $this->assertJson($response->getContent());

        $responseArray = static::getJsonResponseContent($response, 200);
        $this->assertArrayHasKey('success', $responseArray);
        $this->assertArrayHasKey('token', $responseArray);
        $this->assertTrue($responseArray['success']);
        $this->assertIsString($responseArray['token']);
    }
}
