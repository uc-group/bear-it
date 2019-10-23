<?php

namespace App\Project\Model\ProjectAccess\Policy;

use App\Project\Model\Project\Project;
use App\Project\Model\ProjectAccess\Limitation\NotOwnerLimitation;
use App\Project\Model\ProjectAccess\Limitation\NotSelfLimitation;
use BearIt\Access\Model\Policy\PolicyInterface;
use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Policy\PolicyTrait;

class ManageUsersPolicy implements PolicyInterface
{
    use PolicyTrait;

    /**
     * @return AccessFunction
     */
    public static function inviteFunction(): AccessFunction
    {
        return AccessFunction::fromString('project/members/add');
    }

    /**
     * @return AccessFunction
     */
    public static function removeFunction(): AccessFunction
    {
        return AccessFunction::fromString('project/members/remove');
    }

    /**
     * @return AccessFunction
     */
    public static function changeRoleFunction(): AccessFunction
    {
        return AccessFunction::fromString('project/members/change-role');
    }

    /**
     * @param array $functions
     * @param array $limitations
     */
    private function __construct(array $functions, array $limitations = [])
    {
        $this->functions = $functions;
        $this->limitations = $limitations;
    }

    /**
     * @param Project $project
     * @return ManageUsersPolicy[]
     */
    public static function admin(Project $project)
    {
        return [
            new self([
                self::removeFunction(),
                self::changeRoleFunction()
            ], [
                new NotOwnerLimitation($project),
                new NotSelfLimitation($project)
            ]),
            new self([self::inviteFunction()])
        ];
    }

    /**
     * @param Project $project
     * @return ManageUsersPolicy[]
     */
    public static function owner(Project $project)
    {
        return self::admin($project);
    }
}
