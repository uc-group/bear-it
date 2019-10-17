<?php

namespace BearIt\Tests\Access\Model\Limitation;

use BearIt\Access\Model\Limitation\LimitationCollection;
use BearIt\Tests\Access\Traits\AccessMocksTrait;
use PHPUnit\Framework\TestCase;

class LimitationCollectionTest extends TestCase
{
    use AccessMocksTrait;

    public function testUnlimited()
    {
        $collection = new LimitationCollection([]);

        $this->assertTrue($collection->isUnlimited());
    }

    public function testAddsNewLimitation()
    {
        $samples = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(true, true)
        ];

        $collection = new LimitationCollection([$samples[0]]);
        $collection->add($samples[1]);

        foreach ($collection as $i => $limitation) {
            $this->assertSame($samples[$i], $limitation);
        }
    }

    public function testDontAddSameLimitation()
    {
        $sample = $this->createLimitationMock(true, true);

        $collection = new LimitationCollection([$sample]);
        $collection->add($sample);

        $this->assertCount(1, $collection);
    }

    public function testRemovesLimitation()
    {
        $samples = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(true, true)
        ];

        $collection = new LimitationCollection($samples);
        $collection->remove($samples[1]);

        $this->assertCount(1, $collection);
        foreach ($collection as $i => $limitation) {
            $this->assertSame($samples[$i], $limitation);
        }
    }

    public function testNotRemovingNonExistingLimitation()
    {
        $samples = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(true, true)
        ];

        $otherLimitation = $this->createLimitationMock(true, true);

        $collection = new LimitationCollection($samples);
        $collection->remove($otherLimitation);

        $this->assertCount(2, $collection);
        foreach ($collection as $i => $limitation) {
            $this->assertSame($samples[$i], $limitation);
        }
    }

    public function testCombinesIntoUnlimitedWithCurrentUnlimitedOtherNot()
    {
        $collection = new LimitationCollection([]);
        $collection->combine(new LimitationCollection([
            $this->createLimitationMock(true, true)
        ]));

        $this->assertCount(0, $collection);
        $this->assertTrue($collection->isUnlimited());
    }

    public function testCombinesIntoUnlimitedWithCurrentLimitedOtherNot()
    {
        $collection = new LimitationCollection([
            $this->createLimitationMock(true, true)
        ]);
        $collection->combine(new LimitationCollection([]));

        $this->assertCount(0, $collection);
        $this->assertTrue($collection->isUnlimited());
    }

    public function testCombinesLimitations()
    {
        $limitations = [
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(true, true),
            $this->createLimitationMock(true, true),
        ];
        $collection = new LimitationCollection([$limitations[0], $limitations[1]]);
        $collection->combine(new LimitationCollection([$limitations[1], $limitations[2]]));

        $this->assertCount(3, $collection);
        foreach ($collection as $i => $limitation) {
            $this->assertSame($limitations[$i], $limitation);
        }
    }
}
