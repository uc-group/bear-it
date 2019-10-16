<?php

namespace BearIt\Access\Model\Role;

use BearIt\Access\Model\Policy\PolicyInterface;

interface RoleInterface
{
    /**
     * @return PolicyInterface[]
     */
    public function getPolicies(): array;
}
