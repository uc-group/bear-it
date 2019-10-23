<?php

namespace BearIt\Tests\User\Model;

use BearIt\Core\ValueObject\ValueObjectInterface;
use BearIt\Tests\Access\Traits\TestEqualsTrait;
use BearIt\User\Model\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    use TestEqualsTrait;

    public function testToString()
    {
        $userId = UserId::fromString('user1');
        $this->assertSame('user1', $userId->toString());
    }

    /**
     * @return ValueObjectInterface
     */
    protected function createSameSample(): ValueObjectInterface
    {
        return UserId::fromString('user1');
    }

    /**
     * @param bool $sameClass
     * @return ValueObjectInterface|mixed
     */
    protected function createDifferentSample($sameClass = true)
    {
        return $sameClass ? UserId::fromString('user2') : 'some string';
    }
}
