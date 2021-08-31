<?php

namespace Oro\Bundle\ApruveBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Doctrine repository for ApruveSettings entity
 */
class ApruveSettingsRepository extends ServiceEntityRepository
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
