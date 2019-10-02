<?php

namespace App\Project\Model\Project;

use App\Entity\User;

class Project
{
    /**
     * @var ProjectId
     */
    private $projectId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $userRoles;

    /**
     * @var bool
     */
    private $rolesChanged = false;

    /**
     * @param ProjectId $projectId
     * @param string $name
     * @param string|null $description
     * @param array $userRoles
     */
    public function __construct(
        ProjectId $projectId,
        string $name,
        string $description = null,
        array $userRoles = []
    ) {
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->userRoles = $userRoles;
    }

    /**
     * @return ProjectId
     */
    public function id(): ProjectId
    {
        return $this->projectId;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @param User $user
     * @param Role $role
     */
    public function assignUserRole(User $user, Role $role)
    {
        $this->rolesChanged = true;
        $this->userRoles[$user->getId()] = $role;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->rolesChanged = true;
        if (isset($this->userRoles[$user->getId()])) {
            unset($this->userRoles[$user->getId()]);
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isUserAssigned(User $user)
    {
        return isset($this->userRoles[$user->getId()]);
    }

    /**
     * @param User $user
     * @return Role
     */
    public function getUserRole(User $user): Role
    {
        return $this->userRoles[$user->getId()] ?? Role::none();
    }

    /**
     * @return array
     */
    public function users()
    {
        return array_keys($this->userRoles);
    }

    public function roles()
    {
        return $this->userRoles;
    }

    /**
     * @return bool
     */
    public function rolesChanged()
    {
        return $this->rolesChanged;
    }
}