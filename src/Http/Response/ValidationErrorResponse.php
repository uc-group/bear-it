<?php

namespace App\Http\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorResponse extends JsonResponse
{
    public function __construct(ConstraintViolationListInterface $errors, array $headers = [], bool $json = false)
    {
        $messages = [];
        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $key = $error->getPropertyPath();
            $messages[$key] = $error->getMessage();
        }

        parent::__construct([
            'status' => 'ERROR_VALIDATION',
            'errors' => $messages
        ], Response::HTTP_BAD_REQUEST, $headers, $json);
    }
}
