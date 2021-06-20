<?php

namespace BearIt\Tests\Access\Traits;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Limitation\LimitationCollection;
use BearIt\Access\Model\Limitation\LimitationInterface;
use BearIt\Access\Model\Policy\PolicyInterface;
use BearIt\Access\Model\Role\RoleInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait AccessMocksTrait
{
    /**
     * @param $className
     * @return MockObject
     */
    abstract protected function createMock($className);

    /**
     * @param bool $fulfilled
     * @param bool $supports
     * @return MockObject|LimitationInterface
     */
    private function createLimitationMock(bool $fulfilled, bool $supports)
    {
        $mock = $this->createMock(LimitationInterface::class);
        $mock->method('supports')->willReturn($supports);
        $mock->method('isFulfilledBy')->willReturn($fulfilled);

        return $mock;
    }

    /**
     * @param array $policies
     * @return MockObject|RoleInterface
     */
    private function createRoleMock(array $policies)
    {
        $mock = $this->createMock(RoleInterface::class);
        $mock->method('getPolicies')->willReturn($policies);

        return $mock;
    }

    /**
     * @param AccessFunction[] $functions
     * @param array $limitations
     * @return MockObject|PolicyInterface
     */
    private function createPolicyMock(array $functions, array $limitations = [])
    {
        $mock = $this->createMock(PolicyInterface::class);
        $mock->method('getLimitations')->willReturn(new LimitationCollection($limitations));
        $mock->method('getFunctions')->willReturn($functions);

        return $mock;
    }
}
