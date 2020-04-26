<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Integration;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Form\Type\ApruveSettingsType;
use Oro\Bundle\ApruveBundle\Integration\ApruveTransport;
use Symfony\Component\HttpFoundation\ParameterBag;

class ApruveTransportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ApruveTransport
     */
    private $transport;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->transport = new class() extends ApruveTransport {
            public function xgetSettings(): ParameterBag
            {
                return $this->settings;
            }
        };
    }

    public function testInitCorrectlySetsSettingsFromTransportEntity()
    {
        $settings = new ApruveSettings();
        $this->transport->init($settings);
        static::assertSame($settings->getSettingsBag(), $this->transport->xgetSettings());
    }

    public function testGetSettingsFormType()
    {
        static::assertSame(ApruveSettingsType::class, $this->transport->getSettingsFormType());
    }

    public function testGetSettingsEntityFQCN()
    {
        static::assertSame(ApruveSettings::class, $this->transport->getSettingsEntityFQCN());
    }

    public function testGetLabelReturnsCorrectString()
    {
        static::assertSame('oro.apruve.settings.label', $this->transport->getLabel());
    }
}
