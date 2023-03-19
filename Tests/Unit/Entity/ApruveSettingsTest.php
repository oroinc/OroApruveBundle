<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Component\Testing\ReflectionUtil;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;

class ApruveSettingsTest extends \PHPUnit\Framework\TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    /**
     * @dataProvider constructorPropertiesDataProvider
     */
    public function testConstructor(string $property, string $class)
    {
        $settings = new ApruveSettings();
        self::assertInstanceOf($class, ReflectionUtil::getPropertyValue($settings, $property));
    }

    public function constructorPropertiesDataProvider(): array
    {
        return [
            ['labels', ArrayCollection::class],
            ['shortLabels', ArrayCollection::class],
        ];
    }

    public function testAccessors()
    {
        self::assertPropertyAccessors(new ApruveSettings(), [
            ['apruveMerchantId', '7b97ea0172e18cbd4d3bf21e2b525b2d'],
            ['apruveApiKey', '213a9079914f3b5163c6190f31444528'],
            ['apruveTestMode', false],
            ['apruveWebhookToken', '8c02aef5-68df-4458-bad3-e2da636cee90'],
        ]);

        self::assertPropertyCollections(new ApruveSettings(), [
            ['labels', new LocalizedFallbackValue()],
            ['shortLabels', new LocalizedFallbackValue()],
        ]);
    }

    public function testGetSettingsBag()
    {
        $label = (new LocalizedFallbackValue())->setString('Apruve');
        $shortLabel = (new LocalizedFallbackValue())->setString('Apruve (short)');

        $entity = $this->getEntity(
            ApruveSettings::class,
            [
                'apruveMerchantId' => '7b97ea0172e18cbd4d3bf21e2b525b2d',
                'apruveApiKey' => '213a9079914f3b5163c6190f31444528',
                'apruveTestMode' => false,
                'apruveWebhookToken' => '8c02aef5-68df-4458-bad3-e2da636cee90',
                'labels' => [$label],
                'shortLabels' => [$shortLabel],
            ]
        );

        $settings = $entity->getSettingsBag();

        self::assertEquals('7b97ea0172e18cbd4d3bf21e2b525b2d', $settings->get(ApruveSettings::MERCHANT_ID_KEY));
        self::assertEquals('213a9079914f3b5163c6190f31444528', $settings->get(ApruveSettings::API_KEY_KEY));
        self::assertEquals(false, $settings->get(ApruveSettings::TEST_MODE_KEY));
        self::assertEquals('8c02aef5-68df-4458-bad3-e2da636cee90', $settings->get(ApruveSettings::WEBHOOK_TOKEN_KEY));
        self::assertEquals([$label], $settings->get('labels'));
        self::assertEquals([$shortLabel], $settings->get('short_labels'));
    }
}
