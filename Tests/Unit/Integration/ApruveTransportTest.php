<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Integration;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Form\Type\ApruveSettingsType;
use Oro\Bundle\ApruveBundle\Integration\ApruveTransport;
use Oro\Component\Testing\ReflectionUtil;

class ApruveTransportTest extends \PHPUnit\Framework\TestCase
{
    private ApruveTransport $transport;

    protected function setUp(): void
    {
        $this->transport = new ApruveTransport();
    }

    public function testInitCorrectlySetsSettingsFromTransportEntity()
    {
        $settings = new ApruveSettings();
        $this->transport->init($settings);
        self::assertSame(
            $settings->getSettingsBag(),
            ReflectionUtil::getPropertyValue($this->transport, 'settings')
        );
    }

    public function testGetSettingsFormType()
    {
        self::assertSame(ApruveSettingsType::class, $this->transport->getSettingsFormType());
    }

    public function testGetSettingsEntityFQCN()
    {
        self::assertSame(ApruveSettings::class, $this->transport->getSettingsEntityFQCN());
    }

    public function testGetLabelReturnsCorrectString()
    {
        self::assertSame('oro.apruve.settings.label', $this->transport->getLabel());
    }
}
