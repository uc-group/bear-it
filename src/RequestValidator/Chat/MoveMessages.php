<?php

namespace App\RequestValidator\Chat;

use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class MoveMessages extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): array|Constraint
    {
        return new Constraints\Collection([
            'room' => [
                new Constraints\NotBlank(),
                new Constraints\NotNull()
            ],
            'messages' => [
                new Constraints\NotNull(),
                new Constraints\Type('array'),
                new Constraints\Count([
                    'min' => 1
                ]),
                new Constraints\All([
                    new Constraints\Type('string')
                ])
            ]
        ]);
    }

    public function transformData(mixed $data): array
    {
        return [
            'room' => $data['room'],
            'messages' => $data['messages']
        ];
    }
}
