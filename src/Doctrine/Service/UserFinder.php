<?php

namespace App\Doctrine\Service;

use App\Entity\User as UserEntity;
use App\User\Model\User\Avatar;
use App\User\Model\User\User;
use App\User\Model\User\UserId;
use App\User\Service\UserFinderInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserFinder implements UserFinderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $term
     * @return User[]
     */
    public function findByTerm(string $term): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u.id', 'u.username', 'u.name', 'u.avatar');
        $qb->from(UserEntity::class, 'u');
        $qb->where($qb->expr()->orx(
            $qb->expr()->like('u.username', ':term'),
            $qb->expr()->like('u.name', ':term')
        ));
        $qb->setParameter('term', "%{$term}%");

        $result = [];
        foreach ($qb->getQuery()->getArrayResult() as $row) {
            $avatar = $row['avatar'] ?
                Avatar::fromString($row['avatar']) :
                Avatar::createRetroGravatar(md5($row['username']))
            ;

            $result[] = new User(
                UserId::fromString($row['id']),
                $row['username'],
                $row['name'],
                $avatar
            );
        }

        return $result;
    }
}
