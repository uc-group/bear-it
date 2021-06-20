<?php

namespace App\RequestValidator\ProjectMember;

use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class Invite extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): array|Constraint
    {
        return new Constraints\Collection([
            'users' => []
        ]);
    }

    public function getGroups(Request $request): array
    {
        return [];
    }

    public function transformData(mixed $data): array
    {
        return [
            'usernames' => $data['users'] ?? []
        ];
    }
}
