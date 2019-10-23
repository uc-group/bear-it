<?php

namespace BearIt\Access\Model\Policy;

use App\User\Model\User\UserId;
use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Limitation\LimitationCollection;
use BearIt\Access\Model\Limitation\LimitationInterface;

trait PolicyTrait
{
    /**
     * @var AccessFunction[]
     */
    private $functions;

    /**
     * @var LimitationInterface[]
     */
    private $limitations;

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * @return LimitationCollection
     */
    public function getLimitations(): LimitationCollection
    {
        return new LimitationCollection($this->limitations);
    }
}
