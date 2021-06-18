<?php

namespace App\RequestValidator;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Yc\RequestValidationBundle\RequestValidator\RequestValidatorInterface;

class TestRequestValidator implements RequestValidatorInterface
{

    public function getConstraint(Request $request): array|Constraint
    {
        return [];
    }

    public function getGroups(Request $request): array
    {
        return [];
    }

    public function getInvalidRequestResponse(Request $request, ConstraintViolationListInterface $errors): Response
    {
        return new JsonResponse();
    }
}
