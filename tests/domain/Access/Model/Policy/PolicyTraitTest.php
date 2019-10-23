<?php

namespace BearIt\Tests\Access\Model\Policy;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Limitation\LimitationCollection;
use BearIt\Access\Model\Policy\PolicyTrait;
use BearIt\Tests\Access\Traits\AccessMocksTrait;
use PHPUnit\Framework\TestCase;

class PolicyTraitTest extends TestCase
{
    use AccessMocksTrait;

    public function testGetFunctionsSameFunction()
    {
        $functions = [AccessFunction::fromString('one')];
        $policy = new DummyPolicy($functions, []);

        $this->assertSame($functions, $policy->getFunctions());
    }

    public function testGetLimitationsReturnsLimitationCollection()
    {
        $limitation = $this->createLimitationMock(true, false);
        $policy = new DummyPolicy([], [$limitation]);
        $result = $policy->getLimitations();

        $this->assertInstanceOf(LimitationCollection::class, $result);
    }

    public function testGetLimitationsReturnsSameLimitations()
    {
        $limitation = $this->createLimitationMock(true, false);
        $policy = new DummyPolicy([], [$limitation]);
        $result = $policy->getLimitations();

        $this->assertCount(1, $result);
        $this->assertSame($limitation, $result->current());
    }
}
