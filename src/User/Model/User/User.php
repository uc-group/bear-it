<?php

namespace App\User\Model\User;

use BearIt\User\Model\UserId as DomainUserId;

class User
{
    /**
     * @var DomainUserId
     */
    private $userId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Avatar
     */
    private $avatar;

    /**
     * User constructor.
     * @param DomainUserId $userId
     * @param string $username
     * @param string $name
     */
    public function __construct(DomainUserId $userId, string $username, string $name = null, Avatar $avatar = null)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->name = $name;
        $this->avatar = $avatar;
    }

    /**
     * @return DomainUserId
     */
    public function id(): DomainUserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return Avatar|null
     */
    public function avatar(): ?Avatar
    {
        return $this->avatar;
    }

    /**
     * @param string $newName
     */
    public function changeName(string $newName): void
    {
        $this->name = $newName;
    }

    public function changeAvatar(Avatar $avatar): void
    {
        $this->avatar = $avatar;
    }
}
