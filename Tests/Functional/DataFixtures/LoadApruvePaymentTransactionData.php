<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\PropertyAccess;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadApruvePaymentTransactionData extends AbstractFixture implements
    DependentFixtureInterface,
    ContainerAwareInterface
{
    public const AUTHORIZE_TRANSACTION_CHANNEL_1 = 'authorize_transaction_channel_1';
    public const AUTHORIZE_TRANSACTION_CHANNEL_2 = 'authorize_transaction_channel_2';
    public const CAPTURE_TRANSACTION_CHANNEL_2 = 'capture_transaction_channel_2';

    public const PAYMENT_METHOD = 'payment_method';

    private IntegrationIdentifierGeneratorInterface $apruveIdentifierGenerator;

    private array $referenceProperties = ['sourcePaymentTransactionReference'];

    protected array $data = [
        self::AUTHORIZE_TRANSACTION_CHANNEL_1 => [
            'amount' => '1000.00',
            'currency' => 'USD',
            'action' => PaymentMethodInterface::INVOICE,
            'entityReference' => LoadOrders::ORDER_1,
            'entityClass' => Order::class,
            'channelReference' => 'apruve:channel_1',
            'reference' => 'invoice_1',
        ],
        self::AUTHORIZE_TRANSACTION_CHANNEL_2 => [
            'amount' => '1000.00',
            'currency' => 'USD',
            'action' => PaymentMethodInterface::INVOICE,
            'entityReference' => LoadOrders::ORDER_1,
            'entityClass' => Order::class,
            'channelReference' => 'apruve:channel_2',
            'reference' => 'invoice_2',
        ],
        self::CAPTURE_TRANSACTION_CHANNEL_2 => [
            'amount' => '1000.00',
            'currency' => 'USD',
            'action' => PaymentMethodInterface::CAPTURE,
            'entityReference' => LoadOrders::ORDER_1,
            'entityClass' => Order::class,
            'channelReference' => 'apruve:channel_2',
            'reference' => 'invoice_2',
        ],
    ];

    #[\Override]
    public function getDependencies(): array
    {
        return [
            LoadApruveChannelData::class,
            LoadOrders::class
        ];
    }

    #[\Override]
    public function setContainer(ContainerInterface $container = null): void
    {
        $this->apruveIdentifierGenerator  = $container->get('oro_apruve.method.generator.identifier');
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($this->data as $reference => $data) {
            $this->setPaymentMethod($data);
            $this->setEntityIdentifier($data);

            $paymentTransaction = new PaymentTransaction();
            foreach ($data as $property => $value) {
                if (\in_array($property, $this->referenceProperties, true)) {
                    continue;
                }
                $propertyAccessor->setValue($paymentTransaction, $property, $value);
            }
            if (\array_key_exists('sourcePaymentTransactionReference', $data)) {
                $sourcePaymentTransaction = $this->getReference($data['sourcePaymentTransactionReference']);
                $this->setValue($paymentTransaction, 'sourcePaymentTransaction', $sourcePaymentTransaction);
            }

            $this->setReference($reference, $paymentTransaction);
            $manager->persist($paymentTransaction);
        }

        $manager->flush();
    }

    private function setPaymentMethod(array &$data): void
    {
        /** @var Channel $channel */
        $channel = $this->getReference($data['channelReference']);
        unset($data['channelReference']);
        $data['paymentMethod'] = $this->apruveIdentifierGenerator->generateIdentifier($channel);
    }

    private function setEntityIdentifier(array &$data): void
    {
        $data['entityIdentifier'] = $this->getReference($data['entityReference'])->getId();
        unset($data['entityReference']);
    }
}
