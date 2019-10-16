<?php

namespace App\Project\Model\ProjectAccess\Role;

use App\Project\Model\Project\Project;
use App\Project\Model\Project\Role;
use App\Project\Model\ProjectAccess\Policy\ManageUsersPolicy;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use BearIt\Access\Model\Policy\PolicyInterface;
use BearIt\Access\Model\Role\RoleInterface;
use BearIt\Access\Model\Role\RoleTrait;

class ProjectRole implements RoleInterface
{
    use RoleTrait;

    /**
     * ProjectRole constructor.
     * @param array $policies
     */
    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    /**
     * @param Project $project
     * @param Role $role
     * @return ProjectRole
     */
    public static function fromProjectRole(Project $project, Role $role)
    {
        switch (true) {
            case $role->equals(Role::admin()):
                return new self(array_merge(ManageUsersPolicy::admin($project), ProjectPolicy::admin()));

            case $role->equals(Role::owner()):
                return new self(array_merge(ManageUsersPolicy::owner($project), ProjectPolicy::owner()));

            default:
                return new self([]);
        }
    }
}
