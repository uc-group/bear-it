<?php

namespace App\Utils;

use Ramsey\Uuid\Uuid;

class Id
{
    public static function generateUuid(): string
    {
        try {
            return Uuid::uuid4();
        } catch (\Exception $exception) {
            return '';
        }
    }
}