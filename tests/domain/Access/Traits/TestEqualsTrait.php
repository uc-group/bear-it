<?php

namespace BearIt\Tests\Access\Traits;

use BearIt\Core\ValueObject\ValueObjectInterface;

trait TestEqualsTrait
{
    /**
     * @return ValueObjectInterface
     */
    abstract protected function createSameSample(): ValueObjectInterface;

    /**
     * @param bool $sameClass
     * @return ValueObjectInterface|mixed
     */
    abstract protected function createDifferentSample($sameClass = true);

    public function testEquals()
    {
        $sampleOne = $this->createSameSample();
        $sampleTwo = $this->createSameSample();

        $this->assertTrue($sampleOne->equals($sampleTwo));
    }

    public function testNotEquals()
    {
        $sampleOne = $this->createSameSample();
        $sampleTwo = $this->createDifferentSample();

        $this->assertFalse($sampleOne->equals($sampleTwo));
    }

    public function testNotEqualsWithNonFunctionObject()
    {
        $functionOne = $this->createSameSample();

        $this->assertFalse($functionOne->equals($this->createDifferentSample(false)));
    }
}
