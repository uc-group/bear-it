<?php

namespace App\User\Exception;

use App\User\Model\User\Avatar;

class InvalidAvatarDefaultException extends \Exception
{
    public static function create(string $default)
    {
        $valid = array_merge(Avatar::AVAILABLE_DEFAULTS, [Avatar::DEFAULT_404, Avatar::DEFAULT_BLANK]);

        return new self(sprintf(
            'Invalid default value "%s". Must be one of "%s" or valid url.',
            $default,
            implode('", "', $valid)
        ));
    }
}
