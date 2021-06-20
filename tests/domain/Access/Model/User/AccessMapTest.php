<?php

namespace BearIt\Tests\Access\Model\User;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\User\AccessMap;
use BearIt\Tests\Access\Traits\AccessMocksTrait;
use BearIt\User\Model\UserId;
use PHPUnit\Framework\TestCase;

class AccessMapTest extends TestCase
{
    use AccessMocksTrait;

    public function testEmptyRolesGrantNoAccess()
    {
        $accessMap = new AccessMap([]);
        $result = $accessMap->grantsAccess(
            AccessFunction::fromString('anyFunction'),
            UserId::fromString('someUser'),
            null
        );
        $this->assertFalse($result);
    }

    public function testGrantsAccessNotLimited()
    {
        $someFunction = AccessFunction::fromString('someFunction');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction])
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testDeniesAccessOnNonProvidedFunction()
    {
        $someFunction = AccessFunction::fromString('someFunction');
        $otherFunction = AccessFunction::fromString('otherFunction');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction])
            ])
        ]);

        $this->assertFalse($accessMap->grantsAccess($otherFunction, UserId::fromString('anyUser'), null));
    }

    public function testGrantsAccessOnFulfilledLimitation()
    {
        $fulfilledLimitation = $this->createLimitationMock(true, true);
        $someFunction = AccessFunction::fromString('someFunction');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], [$fulfilledLimitation])
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testGrantsAccessOnAllFulfilledLimitations()
    {
        $fulfilledLimitations = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(true, true)
        ];
        $someFunction = AccessFunction::fromString('someFunction');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], $fulfilledLimitations)
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testDeniesAccessOnOneNotFulfilledLimitation()
    {
        $fulfilledLimitations = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(false, true)
        ];
        $someFunction = AccessFunction::fromString('someFunction1');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], $fulfilledLimitations)
            ])
        ]);

        $this->assertFalse($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testIgnoresNotSupportedLimitation()
    {
        $limitations = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(false, false)
        ];
        $someFunction = AccessFunction::fromString('someFunction');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], $limitations)
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testGrantsAccessNoneSupportedLimitations()
    {
        $limitations = [
            $this->createLimitationMock(true, false),
            $this->createLimitationMock(true, false)
        ];
        $someFunction = AccessFunction::fromString('someFunction1');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], $limitations)
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testGrantsAccessOnOneUnlimitedPolicy()
    {
        $someFunction = AccessFunction::fromString('someFunction1');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], [
                    $this->createLimitationMock(false, true)
                ]),
                $this->createPolicyMock([$someFunction], [])
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testGrantsAccessOnAllFulfilledPolicies()
    {
        $someFunction = AccessFunction::fromString('someFunction1');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], [
                    $this->createLimitationMock(true, true)
                ]),
                $this->createPolicyMock([$someFunction], [])
            ])
        ]);

        $this->assertTrue($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

    public function testDeniesAccessOnAllNotFulfilledPolicies()
    {
        $someFunction = AccessFunction::fromString('someFunction1');
        $accessMap = new AccessMap([
            $this->createRoleMock([
                $this->createPolicyMock([$someFunction], [
                    $this->createLimitationMock(false, true)
                ]),
                $this->createPolicyMock([$someFunction], [
                    $this->createLimitationMock(false, true)
                ])
            ])
        ]);

        $this->assertFalse($accessMap->grantsAccess($someFunction, UserId::fromString('anyUser'), null));
    }

}
