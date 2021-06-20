<?php


namespace BearIt\Tests\Access\Model\User;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\User\User;
use BearIt\Tests\Access\Traits\AccessMocksTrait;
use BearIt\User\Model\UserId;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    use AccessMocksTrait;

    public function testGrantsAccess()
    {
        $function = AccessFunction::fromString('someFunction');
        $user = new User(UserId::fromString('user'), [
            $this->createRoleMock([
                $this->createPolicyMock([
                    $function
                ])
            ])
        ]);

        $this->assertTrue($user->isGranted($function, null));
    }

    public function testDeniesAccess()
    {
        $function = AccessFunction::fromString('someFunction');
        $user = new User(UserId::fromString('user'), []);

        $this->assertFalse($user->isGranted($function));
    }

    public function testGrantsAllGranted()
    {
        $userFunctions = [
            AccessFunction::fromString('one'),
            AccessFunction::fromString('two'),
            AccessFunction::fromString('three')
        ];

        $user = new User(UserId::fromString('user'), [
            $this->createRoleMock([
                $this->createPolicyMock($userFunctions)
            ])
        ]);

        $this->assertTrue($user->allGranted([
            $userFunctions[1],
            $userFunctions[2]
        ], null));
    }

    public function testGrantsAnyGranted()
    {
        $userFunctions = [
            AccessFunction::fromString('one')
        ];

        $otherFunctions = [
            AccessFunction::fromString('two'),
            AccessFunction::fromString('three')
        ];

        $user = new User(UserId::fromString('user'), [
            $this->createRoleMock([
                $this->createPolicyMock($userFunctions)
            ])
        ]);

        $this->assertTrue($user->anyGranted(array_merge($userFunctions, $otherFunctions), null));
    }

    public function testDeniesAllGranted()
    {
        $userFunctions = [
            AccessFunction::fromString('one'),
            AccessFunction::fromString('two')
        ];
        $otherFunctions = [
            AccessFunction::fromString('three')
        ];

        $user = new User(UserId::fromString('user'), [
            $this->createRoleMock([
                $this->createPolicyMock($userFunctions)
            ])
        ]);

        $this->assertFalse($user->allGranted(array_merge($userFunctions, $otherFunctions), null));
    }

    public function testDeniesAnyGranted()
    {
        $userFunctions = [
            AccessFunction::fromString('one')
        ];

        $otherFunctions = [
            AccessFunction::fromString('two'),
            AccessFunction::fromString('three')
        ];

        $user = new User(UserId::fromString('user'), [
            $this->createRoleMock([
                $this->createPolicyMock($userFunctions)
            ])
        ]);

        $this->assertFalse($user->anyGranted($otherFunctions, null));
    }
}
