<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\Controller;

use Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures\LoadApruveChannelData;
use Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures\LoadApruvePaymentTransactionData;
use Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures\LoadApruveSettingsData;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class WebhookControllerTest extends WebTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->initClient();

        $this->loadFixtures([
            LoadApruveChannelData::class,
            LoadApruvePaymentTransactionData::class,
        ]);
    }

    public function testNotifyAccessDenied()
    {
        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => 'test'
            ])
        );
        $response = $this->client->getResponse();
        self::assertEquals('Access denied.', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 403);
    }

    public function testNotifyMethodDisabled()
    {
        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_3
            ])
        );
        $response = $this->client->getResponse();
        self::assertEquals('Payment method is disabled.', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 404);
    }

    public function testNotifyBadBody()
    {
        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_1
            ])
        );
        $response = $this->client->getResponse();
        self::assertEquals('Request body can\'t be decoded.', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 400);
    }

    public function testNotifyInvoiceClosedInvalidEventBody()
    {
        $event = [
            'event' => 'invoice.closed',
        ];

        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_1
            ]),
            [],
            [],
            [],
            json_encode($event, JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();
        self::assertEquals('Invalid event body.', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 400);
    }

    public function testNotifyInvoiceClosedInvoiceNotFound()
    {
        $event = [
            'event' => 'invoice.closed',
            'entity' => [
                'id' => 'unknown_id',
            ]
        ];

        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_1
            ]),
            [],
            [],
            [],
            json_encode($event, JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();
        self::assertEquals('Invoice was not found.', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 404);
    }

    public function testNotifyInvoiceClosedEventAlreadyHandled()
    {
        $event = [
            'event' => 'invoice.closed',
            'entity' => [
                'id' => 'invoice_2',
            ]
        ];

        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_2
            ]),
            [],
            [],
            [],
            json_encode($event, JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();
        self::assertEquals('This event already handled.', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 409);
    }

    public function testNotifyInvoiceClosedSuccess()
    {
        $event = [
            'event' => 'invoice.closed',
            'entity' => [
                'id' => 'invoice_1',
            ]
        ];

        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_1
            ]),
            [],
            [],
            [],
            json_encode($event, JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();
        self::assertEquals('', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 200);

        $paymentTransactions = $this->getContainer()->get('oro_payment.repository.payment_transaction')->findBy([
            'action' => PaymentMethodInterface::CAPTURE,
            'reference' => 'invoice_1',
        ]);

        self::assertCount(1, $paymentTransactions);
    }

    /**
     * @dataProvider notifyIgnoredEventsDataProvider
     */
    public function testNotifyIgnoredEvents(string $eventName)
    {
        $event = [
            'event' => $eventName,
        ];

        $this->client->request(
            'POST',
            $this->getUrl('oro_apruve_webhook_notify', [
                'token' => LoadApruveSettingsData::WEBHOOK_TOKEN_1
            ]),
            [],
            [],
            [],
            json_encode($event, JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();
        self::assertEquals('', $response->getContent());
        self::assertHtmlResponseStatusCodeEquals($response, 200);
    }

    public function notifyIgnoredEventsDataProvider(): array
    {
        return [
            ['eventName' => 'order.approved'],
            ['eventName' => 'order.cancled'],
            ['eventName' => 'payment_term.approved'],
        ];
    }
}
