<?php

namespace App\Api\DataValidator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

interface DataValidatorInterface
{
    /**
     * @param Request $request
     * @return Constraint
     */
    public function getConstraint(Request $request): Constraint;

    /**
     * @param Request $request
     * @return array|null
     */
    public function getGroups(Request $request): ?array;

    /**
     * @param Request $request
     * @param $data
     * @return mixed
     */
    public function getData(Request $request, $data);
}