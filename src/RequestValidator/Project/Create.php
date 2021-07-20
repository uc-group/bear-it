<?php

namespace App\RequestValidator\Project;

use App\Entity\Project as ProjectEntity;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\RequestValidator\AbstractRequestValidator;
use App\Validator\Constraint\UniqueField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;

class Create extends AbstractRequestValidator implements DataTransformerInterface
{
    public function getConstraint(Request $request): Constraint
    {
        return new Constraints\Collection([
            'id' => [
                new Constraints\Regex('/^[A-Z][A-Z0-9]*$/'),
                new Constraints\NotBlank(),
                new Constraints\NotNull(),
                new UniqueField([
                    'entityClass' => ProjectEntity::class,
                    'field' => 'id'
                ])
            ],
            'shortId' => [
                new Constraints\Regex('/^[A-Z][A-Z0-9]*$/'),
                new Constraints\NotBlank(),
                new Constraints\NotNull(),
                new UniqueField([
                    'entityClass' => ProjectEntity::class,
                    'field' => 'shortId'
                ])
            ],
            'name' => [
                new Constraints\NotNull(),
                new Constraints\NotBlank(),
                new Constraints\Length([
                    'min' => 3,
                    'max' => 80
                ]),
                new UniqueField([
                    'entityClass' => ProjectEntity::class,
                    'field' => 'name'
                ])
            ],
            'description' => [],
            'color' => [
                new Constraints\Regex('/#([0-9a-f]{3}|[0-9a-f]{6})$/i')
            ]
        ]);
    }

    public function transformData(mixed $data): array
    {
        $id = ProjectId::fromString($data['id']);

        return [
            'project' => new Project($id, $data['shortId'], $data['name'], $data['description'], $data['color'])
        ];
    }
}
