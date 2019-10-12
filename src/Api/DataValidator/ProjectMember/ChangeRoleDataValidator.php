<?php


namespace App\Api\DataValidator\ProjectMember;


use App\Api\DataValidator\DataValidatorInterface;
use App\Project\Model\Project\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class ChangeRoleDataValidator implements DataValidatorInterface
{
    /**
     * @param Request $request
     * @return Constraint
     */
    public function getConstraint(Request $request): Constraint
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