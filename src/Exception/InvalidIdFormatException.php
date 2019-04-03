<?php

namespace App\Exception;

class InvalidIdFormatException extends BearItException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('"%s" is not a valid ID format.', $id));
    }
}