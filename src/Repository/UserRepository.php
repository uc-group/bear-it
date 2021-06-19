<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->findOneBy([
            'username' => $username
        ]);
    }

    /**
     * @param array $usernames
     * @return User[]
     */
    public function findManyByUsernames(array $usernames): array
    {
        return $this->findBy([
            'username' => $usernames
        ]);
    }
}
