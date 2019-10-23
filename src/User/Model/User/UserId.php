<?php

namespace App\User\Model\User;

use App\Utils\Id;
use BearIt\User\Model\UserId as DomainUserId;

class UserId extends DomainUserId
{
    /**
     * @return self
     */
    public static function new(): self
    {
        return self::fromString(Id::generateUuid());
    }
}
