<?php

namespace App\RequestValidator\Pages;

use App\Pages\Model\Page;
use App\Project\Model\Project\ProjectId;
use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class CreateBook extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): Constraint
    {
        return new Constraints\Collection([
            'project' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 3,
                    'max' => 36
                ])
            ],
            'name' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 2,
                    'max' => 80
                ])
            ]
        ]);
    }

    public function transformData(mixed $data): array
    {
        return [
            'name' => $data['name'],
            'projectId' => ProjectId::fromString($data['project'])
        ];
    }
}
