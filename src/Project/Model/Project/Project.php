<?php

namespace App\Project\Model\Project;

use App\Project\Model\Component\ComponentId;
use BearIt\User\Model\UserId;

class Project
{
    private string $rolesHash;

    /**
     * @param Role[] $userRoles
     * @param ComponentId[] $components
     */
    public function __construct(
        private ProjectId $projectId,
        private string $name,
        private ?string $description = null,
        private ?string $color = null,
        private array $userRoles = [],
        private array $components = []
    ) {
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
     * @param UserId $user
     * @param Role $role
     */
    public function assignUserRole(UserId $user, Role $role)
    {
        $this->userRoles[$user->toString()] = $role;
    }

    /**
     * @param UserId $user
     */
    public function addUser(UserId $user): void
    {
        if (isset($this->userRoles[$user->toString()])) {
            return;
        }

        $this->assignUserRole($user, Role::member());
    }

    /**
     * @param UserId $user
     */
    public function removeUser(UserId $user)
    {
        if (isset($this->userRoles[$user->toString()])) {
            unset($this->userRoles[$user->toString()]);
        }
    }

    /**
     * @param UserId $user
     * @return bool
     */
    public function isUserAssigned(UserId $user)
    {
        return isset($this->userRoles[$user->toString()]);
    }

    /**
     * @param UserId $user
     * @return Role
     */
    public function getUserRole(UserId $user): Role
    {
        return $this->userRoles[$user->toString()] ?? Role::none();
    }

    /**
     * @param UserId $userId
     * @return bool
     */
    public function isOwner(UserId $userId): bool
    {
        return $this->userRoles[$userId->toString()]->equals(Role::owner());
    }

    /**
     * @return array
     */
    public function users()
    {
        return array_keys($this->userRoles);
    }

    /**
     * @return Role[]|array
     */
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

    public function assignComponent(ComponentId $component): void
    {
        $this->components[] = $component;
    }

    /**
     * @return ComponentId[]
     */
    public function components(): array
    {
        return $this->components;
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
