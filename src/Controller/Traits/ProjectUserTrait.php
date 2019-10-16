<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Exception\UserNotLoggedInException;
use App\Project\Model\Project\Project;
use App\Project\Model\ProjectAccess\Role\ProjectRole;
use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\User\User as UserAccess;
use BearIt\User\Model\UserId;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait ProjectUserTrait
{
    /**
     * @return User|string|null
     */
    abstract protected function getUser();

    /**
     * @return AccessDeniedException
     */
    abstract protected function createAccessDeniedException();

    /**
     * @return UserId
     * @throws UserNotLoggedInException
     */
    private function currentUserId(): UserId
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new UserNotLoggedInException();
        }

        return $this->getUser()->getId();
    }

    /**
     * @param Project $project
     * @return UserAccess
     */
    private function getUserAccess(Project $project)
    {
        try {
            $userId = $this->currentUserId();

            return new UserAccess($userId, [
                ProjectRole::fromProjectRole($project, $project->getUserRole($userId))
            ]);
        } catch (UserNotLoggedInException $exception) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param UserAccess $userAccess
     * @param AccessFunction $function
     * @param $subject
     */
    private function throwAccessDeniedUnlessGranted(UserAccess $userAccess, AccessFunction $function, $subject)
    {
        if (!$userAccess->isGranted($function, $subject)) {
            throw $this->createAccessDeniedException();
        }
    }
}
