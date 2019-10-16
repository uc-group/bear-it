<?php

namespace BearIt\Access\Model\Role;

use BearIt\Access\Model\Policy\PolicyInterface;

trait RoleTrait
{
    /**
     * @var PolicyInterface[]
     */
    private $policies;

    /**
     * @return array
     */
    public function getPolicies(): array
    {
        return $this->policies;
    }
}
