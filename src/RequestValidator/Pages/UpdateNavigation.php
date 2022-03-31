<?php

namespace App\RequestValidator\Pages;

use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class UpdateNavigation extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): Constraint
    {
        return new Constraints\Collection([
            'navigation' => [
                new Constraints\NotNull()
            ],
        ]);
    }

    public function transformData(mixed $data): array
    {
        return [
            'navigation' => $data['navigation']
        ];
    }
}