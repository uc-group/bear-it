<?php

namespace App\Api\DataValidator\Project;

use App\Api\DataValidator\DataValidatorInterface;
use App\Entity\Project;
use App\Validator\Constraint\UniqueField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class CreateDataValidator implements DataValidatorInterface
{
    /**
     * @param Request $request
     * @return Constraint
     */
    public function getConstraint(Request $request): Constraint
    {
        return new Constraints\Collection([
            'id' => [
                new Constraints\Regex('/^[A-Z][A-Z0-9]*$/'),
                new Constraints\NotBlank(),
                new Constraints\NotNull(),
                new UniqueField([
                    'entityClass' => Project::class,
                    'field' => 'id'
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
                    'entityClass' => Project::class,
                    'field' => 'name'
                ])
            ],
            'description' => [],
            'color' => [
                new Constraints\Regex('/#([0-9a-f]{3}|[0-9a-f]{6})$/i')
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $data
     * @return mixed
     */
    public function getData(Request $request, $data)
    {
        return $data;
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getGroups(Request $request): ?array
    {
        return null;
    }
}
