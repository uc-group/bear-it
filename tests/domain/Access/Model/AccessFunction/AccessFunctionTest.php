<?php

namespace BearIt\Tests\Access\Model\AccessFunction;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Core\ValueObject\ValueObjectInterface;
use BearIt\Tests\Access\Traits\TestEqualsTrait;
use PHPUnit\Framework\TestCase;

class AccessFunctionTest extends TestCase
{
    use TestEqualsTrait;

    protected function createSameSample(): ValueObjectInterface
    {
        return AccessFunction::fromString('one');
    }

    protected function createDifferentSample($sameClass = true)
    {
        return $sameClass ? AccessFunction::fromString('two') : 'two';
    }
}
