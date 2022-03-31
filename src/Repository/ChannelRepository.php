<?php

namespace App\Repository;

use App\Project\Model\Project\ProjectId;
use Doctrine\ORM\EntityRepository;

class ChannelRepository extends EntityRepository
{
    public function findByProject(ProjectId $projectId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->like('c.roomId', ':projectRoom'));
        $qb->setParameter('projectRoom', sprintf('chat/%s%%', $projectId->toString()));

        return $qb->getQuery()->getResult();
    }
}
