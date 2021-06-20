<?php

namespace App\Project\Model\ProjectAccess\Limitation;

use App\Project\Model\Project\Project;
use BearIt\Access\Model\Limitation\LimitationInterface;
use BearIt\User\Model\UserId;

class NotOwnerLimitation implements LimitationInterface
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @param UserId $userId
     * @param UserId $subject
     * @return bool
     */
    public function isFulfilledBy(UserId $userId, $subject): bool
    {
        return !$this->project->isOwner($subject);
    }

    /**
     * @param $subject
     * @return bool
     */
    public function supports($subject): bool
    {
        return $subject instanceof UserId;
    }
}
