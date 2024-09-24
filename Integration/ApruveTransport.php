<?php

namespace Oro\Bundle\ApruveBundle\Integration;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Form\Type\ApruveSettingsType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class ApruveTransport implements TransportInterface
{
    /**
     * @var ParameterBag
     */
    protected $settings;

    #[\Override]
    public function init(Transport $transportEntity)
    {
        $this->settings = $transportEntity->getSettingsBag();
    }

    #[\Override]
    public function getSettingsFormType()
    {
        return ApruveSettingsType::class;
    }

    #[\Override]
    public function getSettingsEntityFQCN()
    {
        return ApruveSettings::class;
    }

    #[\Override]
    public function getLabel()
    {
        return 'oro.apruve.settings.label';
    }
}
