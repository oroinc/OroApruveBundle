<?php

namespace Oro\Bundle\ApruveBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class ApruveSettingsRepository extends EntityRepository
{
    /**
     * @var AclHelper
     */
    private $aclHelper;

    public function setAclHelper(AclHelper $aclHelper)
    {
        $this->aclHelper = $aclHelper;
    }

    /**
     * @return ApruveSettings[]
     */
    public function findEnabledSettings()
    {
        $qb = $this->createQueryBuilder('settings');
        $qb
            ->innerJoin('settings.channel', 'channel')
            ->andWhere($qb->expr()->eq('channel.enabled', ':channelEnabled'))
            ->setParameter('channelEnabled', true);

        return $this->aclHelper->apply($qb)->getResult();
    }
}
