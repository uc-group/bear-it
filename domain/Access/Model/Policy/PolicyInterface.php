<?php

namespace BearIt\Access\Model\Policy;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Limitation\LimitationCollection;

interface PolicyInterface
{
    /**
     * @return AccessFunction[]
     */
    public function getFunctions(): array;

    /**
     * @return LimitationCollection
     */
    public function getLimitations(): LimitationCollection;
}
