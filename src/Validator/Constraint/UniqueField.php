<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class UniqueField extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value is already used';

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $entityClass;
}
