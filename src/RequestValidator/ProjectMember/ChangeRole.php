<?php

namespace App\RequestValidator\ProjectMember;

use App\Project\Model\Project\Role;
use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class ChangeRole extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): array|Constraint
    {
        return new Constraints\Collection([
            'member' => [new Constraints\NotNull()],
            'role' => [new Constraints\Choice([
                'choices' => [
                    Role::admin()->toString(),
                    Role::member()->toString()
                ]
            ])]
        ]);
    }

    public function getGroups(Request $request): array
    {
        return [];
    }

    public function transformData(mixed $data): array
    {
        return [
            'member' => $data['member'],
            'role' => Role::fromString($data['role'])
        ];
    }
}
