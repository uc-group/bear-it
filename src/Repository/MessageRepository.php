<?php

namespace App\Repository;

use App\Entity\Chat\Message;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * @return Message[]
     */
    public function findBeforeTimestamp(string $roomId, int $timestamp, int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->where('m.postedAt < :timestamp');
        $qb->andWhere('m.roomId = :roomId');
        $qb->orderBy('m.postedAt', 'DESC');
        $qb->setParameters([
            'timestamp' => $timestamp,
            'roomId' => $roomId
        ]);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
