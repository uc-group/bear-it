<?php

namespace BearIt\Tests\Access\Model\Role;

use BearIt\Access\Model\Role\RoleTrait;

class DummyRole
{
    use RoleTrait;

    public function __construct($policies)
    {
        $this->policies = $policies;
    }
}
