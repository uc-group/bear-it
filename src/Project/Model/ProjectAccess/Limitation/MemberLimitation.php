<?php

namespace App\Project\Model\ProjectAccess\Limitation;

use App\Project\Model\Project\Project;
use BearIt\Access\Model\Limitation\LimitationInterface;
use BearIt\User\Model\UserId;

class MemberLimitation implements LimitationInterface
{
    /**
     * @param UserId $currentUser
     * @param Project $subject
     * @return bool
     */
    public function isFulfilledBy(UserId $currentUser, $subject): bool
    {
        return $subject->isUserAssigned($currentUser);
    }

    /**
     * @param $subject
     * @return bool
     */
    public function supports($subject): bool
    {
        return $subject instanceof Project;
    }
}
