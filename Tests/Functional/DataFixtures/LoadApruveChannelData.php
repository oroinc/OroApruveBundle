<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadOrganization;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadUser;

class LoadApruveChannelData extends AbstractFixture implements DependentFixtureInterface
{
    private const CHANNEL_DATA = [
        [
            'name' => 'Apruve1',
            'type' => 'apruve',
            'enabled' => true,
            'transport' => 'apruve:transport_1',
            'reference' => 'apruve:channel_1',
        ],
        [
            'name' => 'Apruve2',
            'type' => 'apruve',
            'enabled' => true,
            'transport' => 'apruve:transport_2',
            'reference' => 'apruve:channel_2',
        ],
        [
            'name' => 'Apruve3',
            'type' => 'apruve',
            'enabled' => false,
            'transport' => 'apruve:transport_3',
            'reference' => 'apruve:channel_3',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [LoadApruveSettingsData::class, LoadOrganization::class, LoadUser::class];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::CHANNEL_DATA as $data) {
            $entity = new Channel();
            $entity->setName($data['name']);
            $entity->setType($data['type']);
            $entity->setEnabled($data['enabled']);
            $entity->setDefaultUserOwner($this->getReference(LoadUser::USER));
            $entity->setOrganization($this->getReference(LoadOrganization::ORGANIZATION));
            $entity->setTransport($this->getReference($data['transport']));
            $this->setReference($data['reference'], $entity);
            $manager->persist($entity);
        }
        $manager->flush();
    }
}
