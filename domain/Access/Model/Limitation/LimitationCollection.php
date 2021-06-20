<?php

namespace BearIt\Access\Model\Limitation;

class LimitationCollection implements \Iterator
{
    /**
     * @var LimitationInterface[]
     */
    private $limitations;

    /**
     * @param LimitationInterface[] $limitations
     */
    public function __construct(array $limitations = [])
    {
        $this->limitations = $limitations;
    }

    /**
     * @return bool
     */
    public function isUnlimited()
    {
        return empty($this->limitations);
    }

    /**
     * @param LimitationInterface $limitation
     */
    public function add(LimitationInterface $limitation)
    {
        foreach ($this->limitations as $currentLimitation) {
            if ($currentLimitation === $limitation) {
                return;
            }
        }

        $this->limitations[] = $limitation;
    }

    /**
     * @param LimitationInterface $limitation
     */
    public function remove(LimitationInterface $limitation)
    {
        foreach ($this->limitations as $i => $currentLimitation) {
            if ($currentLimitation === $limitation) {
                unset($this->limitations[$i]);

                return;
            }
        }
    }

    /**
     * @param LimitationCollection $collection
     */
    public function combine(LimitationCollection $collection)
    {
        if ($this->isUnlimited() || $collection->isUnlimited()) {
            $this->limitations = [];
            return;
        }

        foreach ($collection as $limitation) {
            $this->add($limitation);
        }
    }

    /**
     * @return LimitationInterface
     */
    public function current()
    {
        return current($this->limitations);
    }

    /**
     * @return LimitationInterface
     */
    public function next()
    {
        return next($this->limitations);
    }

    /**
     * @return int
     */
    public function key()
    {
        return key($this->limitations);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return key($this->limitations) !== null;
    }

    /**
     * @return LimitationInterface
     */
    public function rewind()
    {
        return reset($this->limitations);
    }
}
