<?php

namespace App\RequestValidator\Chat\Channel;

use App\Entity\Chat\Channel;
use App\RequestValidator\AbstractRequestValidator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class Create extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): Constraint
    {
        return new Constraints\Collection([
            'name' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 1,
                    'max' => 255
                ])
            ],
            'room' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 1,
                    'max' => 255
                ])
            ],
        ]);
    }

    public function transformData(mixed $data): array
    {
        return [
            'channel' => new Channel(Uuid::uuid4(), $data['room'], $data['name'])
        ];
    }
}
