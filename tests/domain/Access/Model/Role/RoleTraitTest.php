<?php

namespace BearIt\Tests\Access\Model\Role;

use BearIt\Tests\Access\Traits\AccessMocksTrait;
use PHPUnit\Framework\TestCase;

class RoleTraitTest extends TestCase
{
    use AccessMocksTrait;

    public function testGetsSamePolicies()
    {
        $policy = $this->createPolicyMock([], []);
        $role = new DummyRole([$policy]);

        $this->assertSame($policy, $role->getPolicies()[0]);
    }
}
