<?php

namespace BearIt\Core\ValueObject;

interface ValueObjectInterface
{
    /**
     * @param $other
     * @return bool
     */
    public function equals($other): bool;
}
