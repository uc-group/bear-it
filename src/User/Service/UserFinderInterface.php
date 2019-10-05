<?php

namespace App\User\Service;

use App\User\Model\User\User;

interface UserFinderInterface
{
    /**
     * @param string $term
     * @return User[]
     */
    public function findByTerm(string $term): array;
}
