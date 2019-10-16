<?php

namespace BearIt\Access\Model\AccessFunction;

class PermissionSet
{
    /**
     * @var AccessFunction[]
     */
    private $functions;

    /**
     * @var mixed
     */
    private $subject;

    /**
     * @param AccessFunction[] $functions
     * @param $subject
     */
    public function __construct(array $functions, $subject)
    {
        $this->functions = $functions;
        $this->subject = $subject;
    }

    /**
     * @return AccessFunction[]
     */
    public function functions()
    {
        return $this->functions;
    }

    /**
     * @return mixed
     */
    public function subject()
    {
        return $this->subject;
    }
}
