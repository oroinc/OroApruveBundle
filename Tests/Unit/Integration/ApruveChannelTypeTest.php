<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Integration;

use Oro\Bundle\ApruveBundle\Integration\ApruveChannelType;

class ApruveChannelTypeTest extends \PHPUnit\Framework\TestCase
{
    private ApruveChannelType $channel;

    #[\Override]
    protected function setUp(): void
    {
        $this->channel = new ApruveChannelType();
    }

    public function testGetLabelReturnsCorrectString()
    {
        self::assertSame('oro.apruve.channel_type.label', $this->channel->getLabel());
    }

    public function testGetIcon()
    {
        self::assertSame('bundles/oroapruve/img/apruve-logo.png', $this->channel->getIcon());
    }
}
