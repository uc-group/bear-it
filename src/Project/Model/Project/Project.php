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
     * @var string
     */
    private $color;

    /**
     * @var Role[]
     */
    private $userRoles;

    /**
     * @var string
     */
    private $rolesHash;

    /**
     * @param ProjectId $projectId
     * @param string $name
     * @param string|null $description
     * @param string|null $color
     * @param Role[] $userRoles
     */
    public function __construct(
        ProjectId $projectId,
        string $name,
        string $description = null,
        string $color = null,
        array $userRoles = []
    ) {
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
        $this->userRoles = $userRoles;
        $this->rolesHash = $this->calculateRolesHash();
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
     * @return string|null
     */
    public function color(): ?string
    {
        return $this->color;
    }

    /**
     * @param User $user
     * @param Role $role
     */
    public function assignUserRole(User $user, Role $role)
    {
        $this->userRoles[$user->getId()] = $role;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canManageUsers(User $user): bool
    {
        $role = $this->getUserRole($user);

        return $role->equals(Role::admin()) || $role->equals(Role::owner());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRemove(User $user): bool
    {
        $role = $this->getUserRole($user);

        return $role->equals(Role::admin()) || $role->equals(Role::owner());
    }

    /**
     * @param User $user
     */
    public function addUser(User $user): void
    {
        if (isset($this->userRoles[$user->getId()])) {
            return;
        }

        $this->assignUserRole($user, Role::member());
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
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
        return $this->rolesHash !== $this->calculateRolesHash();
    }

    /**
     * @return string
     */
    private function calculateRolesHash()
    {
        $roles = [];
        foreach ($this->userRoles as $userId => $role) {
            $roles[] = sprintf('%s;%s', $userId, $role->toString());
        }
        sort($roles);

        return md5(implode('|', $roles));
    }
}
