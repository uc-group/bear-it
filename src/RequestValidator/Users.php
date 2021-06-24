<?php

namespace App\RequestValidator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class Users extends AbstractRequestValidator implements DataTransformerInterface
{

    public function transformData(mixed $data): array
    {
        return [
            'userIds' => $data['users']
        ];
    }

    public function getConstraint(Request $request): array|Constraint
    {
        return [];
    }
}
