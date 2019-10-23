<?php

namespace BearIt\Tests\Access\Model\Policy;

use BearIt\Access\Model\Policy\PolicyTrait;

class DummyPolicy
{
    use PolicyTrait;

    public function __construct($functions, $limitations)
    {
        $this->functions = $functions;
        $this->limitations = $limitations;
    }
}
