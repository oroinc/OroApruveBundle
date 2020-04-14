<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Entity\Repository\ApruveSettingsRepository;
use Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures\LoadApruveChannelData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class ApruveSettingsRepositoryTest extends WebTestCase
{
    /**
     * @var ApruveSettingsRepository
     */
    private $repository;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], static::generateBasicAuthHeader());

        $this->loadFixtures([
            LoadApruveChannelData::class,
        ]);

        $this->repository = static::getContainer()
            ->get('doctrine')
            ->getManagerForClass(ApruveSettings::class)
            ->getRepository(ApruveSettings::class);
    }

    public function testFindEnabledSettings()
    {
        $settingsByEnabledChannel = $this->repository->findEnabledSettings();

        static::assertCount(2, $settingsByEnabledChannel);
        static::assertContains($this->getReference('apruve:transport_1'), $settingsByEnabledChannel);
        static::assertContains($this->getReference('apruve:transport_2'), $settingsByEnabledChannel);
    }
}
