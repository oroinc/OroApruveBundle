<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Entity\Repository\ApruveSettingsRepository;
use Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures\LoadApruveChannelData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class ApruveSettingsRepositoryTest extends WebTestCase
{
    private ApruveSettingsRepository $repository;

    protected function setUp(): void
    {
        $this->initClient([], self::generateBasicAuthHeader());

        $this->loadFixtures([
            LoadApruveChannelData::class,
        ]);

        $this->repository = self::getContainer()
            ->get('doctrine')
            ->getManagerForClass(ApruveSettings::class)
            ->getRepository(ApruveSettings::class);
    }

    public function testFindEnabledSettings()
    {
        $settingsByEnabledChannel = $this->repository->findEnabledSettings();

        self::assertCount(2, $settingsByEnabledChannel);
        self::assertContains($this->getReference('apruve:transport_1'), $settingsByEnabledChannel);
        self::assertContains($this->getReference('apruve:transport_2'), $settingsByEnabledChannel);
    }
}
