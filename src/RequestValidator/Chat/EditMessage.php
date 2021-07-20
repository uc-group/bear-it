<?php

namespace App\RequestValidator\Chat;

use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class EditMessage extends AbstractRequestValidator
{
    public function getConstraint(Request $request): array|Constraint
    {
        return new Constraints\Collection([
            'author' => [
                new Constraints\NotBlank(),
                new Constraints\NotNull()
            ],
            'id' => [
                new Constraints\NotBlank(),
                new Constraints\NotNull()
            ],
            'content' => [
                new Constraints\NotBlank(),
                new Constraints\NotNull()
            ]
        ]);
    }
}
