<?php

namespace App\Task\Model\Task;

use BearIt\User\Model\UserId;

class BoundedUser
{
    protected function __construct(
        private UserId $userId
    ) {}

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public static function fromUserId(UserId $id): static
    {
        return new static($id);
    }

    public function equals($other): bool
    {
        if (!$other instanceof static) {
            return false;
        }

        return $this->userId->equals($other->userId);
    }
}
