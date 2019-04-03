<?php

namespace App\Utils;

use DateTime as PhpDateTime;
use Exception;

class DateTime
{
    /**
     * @return PhpDateTime|null
     */
    public static function now()
    {
        try {
            return new PhpDateTime();
        } catch (Exception $exception) {
            return null;
        }
    }
}