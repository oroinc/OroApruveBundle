<?php

namespace Oro\Bundle\ApruveBundle\Integration;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;
use Oro\Bundle\IntegrationBundle\Provider\IconAwareIntegrationInterface;

class ApruveChannelType implements ChannelInterface, IconAwareIntegrationInterface
{
    public const TYPE = 'apruve';

    #[\Override]
    public function getLabel()
    {
        return 'oro.apruve.channel_type.label';
    }

    #[\Override]
    public function getIcon()
    {
        return 'bundles/oroapruve/img/apruve-logo.png';
    }
}
