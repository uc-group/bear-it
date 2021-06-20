<?php

namespace BearIt\Access\Model\Limitation;

use BearIt\User\Model\UserId;

interface LimitationInterface
{
    /**
     * @param UserId $currentUser
     * @param $subject
     * @return bool
     */
    public function isFulfilledBy(UserId $currentUser, $subject): bool;

    /**
     * @param $subject
     * @return bool
     */
    public function supports($subject): bool;
}
