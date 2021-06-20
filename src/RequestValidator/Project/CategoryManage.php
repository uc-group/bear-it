<?php

namespace App\RequestValidator\Project;

use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class CategoryManage extends AbstractRequestValidator implements DataTransformerInterface
{

    public function getConstraint(Request $request): array|Constraint
    {
        return new Constraints\Collection([
            'component' => [new Constraints\NotNull()]
        ]);
    }

    public function transformData(mixed $data): array
    {
        return [
            'component' => $data['component']
        ];
    }
}
