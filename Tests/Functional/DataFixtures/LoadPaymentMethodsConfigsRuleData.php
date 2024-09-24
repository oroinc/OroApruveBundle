<?php

namespace Oro\Bundle\ApruveBundle\Tests\Functional\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\PaymentBundle\Entity\PaymentMethodConfig;
use Oro\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRule;
use Oro\Bundle\PaymentBundle\Tests\Functional\Entity\DataFixtures\LoadPaymentMethodsConfigsRuleData as BaseFixture;

class LoadPaymentMethodsConfigsRuleData extends BaseFixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        parent::load($manager);

        $methodConfig = new PaymentMethodConfig();
        /** @var Channel $channel */
        $channel = $this->getReference('apruve:channel_1');
        $methodConfig->setType($this->getPaymentMethodIdentifier($channel));

        /** @var PaymentMethodsConfigsRule $methodsConfigsRule */
        $methodsConfigsRule = $this->getReference('payment.payment_methods_configs_rule.1');
        $methodsConfigsRule->addMethodConfig($methodConfig);

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return array_merge(parent::getDependencies(), [LoadApruveChannelData::class]);
    }

    private function getPaymentMethodIdentifier(Channel $channel): string
    {
        return $this->container->get('oro_apruve.method.generator.identifier')
            ->generateIdentifier($channel);
    }
}
