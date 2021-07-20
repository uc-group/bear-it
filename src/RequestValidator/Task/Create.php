<?php

namespace App\RequestValidator\Task;

use App\Entity\Project as ProjectEntity;
use App\Project\Model\Project\ProjectId;
use App\RequestValidator\AbstractRequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class Create extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): Constraint
    {
        return new Constraints\Collection([
            'title' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 3,
                    'max' => 80
                ])
            ],
            'description' => [],
            'projectId' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 3,
                    'max' => 36
                ])
            ]
        ]);
    }

    public function transformData(mixed $data): array
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'],
            'projectId' => ProjectId::fromString($data['projectId'])
        ];
    }
}