<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Oro\Component\Testing\Unit\EntityTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadApruvePaymentTransactionData extends AbstractFixture implements
    DependentFixtureInterface,
    ContainerAwareInterface
{
    use EntityTrait;

    const AUTHORIZE_TRANSACTION_CHANNEL_1 = 'authorize_transaction_channel_1';
    const AUTHORIZE_TRANSACTION_CHANNEL_2 = 'authorize_transaction_channel_2';
    const CAPTURE_TRANSACTION_CHANNEL_2 = 'capture_transaction_channel_2';

    const PAYMENT_METHOD = 'payment_method';

    /**
     * @var array
     */
    private $referenceProperties = ['sourcePaymentTransactionReference'];

    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    private $apruveIdentifierGenerator;

    /**
     * @var array
     */
    protected $data = [
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

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            LoadApruveChannelData::class,
            LoadOrders::class
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->apruveIdentifierGenerator  = $container->get('oro_apruve.method.generator.identifier');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $reference => $data) {
            $paymentTransaction = new PaymentTransaction();

            $this->setPaymentMethod($data);
            $this->setEntityIdentifier($data);

            foreach ($data as $property => $value) {
                if ($this->isReferenceProperty($property)) {
                    continue;
                }

                $this->setValue($paymentTransaction, $property, $value);
            }

            $this->handleReferenceProperties($paymentTransaction, $data);

            $this->setReference($reference, $paymentTransaction);

            $manager->persist($paymentTransaction);
        }

        $manager->flush();
    }

    private function setPaymentMethod(array &$data)
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

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param array              $data
     *
     * @return void
     */
    private function handleReferenceProperties(PaymentTransaction $paymentTransaction, array $data)
    {
        if (array_key_exists('sourcePaymentTransactionReference', $data)) {
            $sourcePaymentTransaction = $this->getReference($data['sourcePaymentTransactionReference']);

            $this->setValue($paymentTransaction, 'sourcePaymentTransaction', $sourcePaymentTransaction);
        }
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    private function isReferenceProperty($property)
    {
        return in_array($property, $this->referenceProperties, true);
    }
}
