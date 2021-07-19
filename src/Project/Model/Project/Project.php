<?php

namespace App\Project\Model\Project;

use App\Entity\Task;
use BearIt\User\Model\UserId;

class Project
{
    private string $rolesHash;

    /**
     * @param Role[] $userRoles
     * @param string[] $components
     */
    public function __construct(
        private ProjectId $projectId,
        private string $shortId,
        private string $name,
        private ?string $description = null,
        private ?string $color = null,
        private array $userRoles = [],
        private array $components = [],
        private array $tasks = []
    ) {
        $this->rolesHash = $this->calculateRolesHash();
    }

    public function id(): ProjectId
    {
        return $this->projectId;
    }

    public function shortId(): string
    {
        return $this->shortId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function color(): ?string
    {
        return $this->color;
    }

    public function assignUserRole(UserId $user, Role $role): void
    {
        $this->userRoles[$user->toString()] = $role;
    }

    public function addUser(UserId $user): void
    {
        if (isset($this->userRoles[$user->toString()])) {
            return;
        }

        $this->assignUserRole($user, Role::member());
    }

    public function removeUser(UserId $user): void
    {
        if (isset($this->userRoles[$user->toString()])) {
            unset($this->userRoles[$user->toString()]);
        }
    }

    public function isUserAssigned(UserId $user): bool
    {
        return isset($this->userRoles[$user->toString()]);
    }

    public function getUserRole(UserId $user): Role
    {
        return $this->userRoles[$user->toString()] ?? Role::none();
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->userRoles[$userId->toString()]->equals(Role::owner());
    }

    public function users(): array
    {
        return array_keys($this->userRoles);
    }

    /**
     * @return Role[]
     */
    public function roles(): array
    {
        return $this->userRoles;
    }

    /**
     * @return Task[]
     */
    public function tasks(): array
    {
        return $this->tasks;
    }

    public function rolesChanged(): bool
    {
        return $this->rolesHash !== $this->calculateRolesHash();
    }

    public function assignComponent(string $component): void
    {
        $this->components[] = $component;
        $this->components = array_values(array_unique($this->components));
    }

    public function removeComponent(string $component): void
    {
        $this->components = array_values(array_diff($this->components, [$component]));
    }

    /**
     * @return string[]
     */
    public function components(): array
    {
        return $this->components;
    }

    private function calculateRolesHash(): string
    {
        $roles = [];
        foreach ($this->userRoles as $userId => $role) {
            $roles[] = sprintf('%s;%s', $userId, $role->toString());
        }
        sort($roles);

        return md5(implode('|', $roles));
    }
}
