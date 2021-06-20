<?php

namespace App\RequestValidator;

use App\Http\Response\ValidationErrorResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Yc\RequestValidationBundle\DataReceiver\DataReceiverInterface;
use Yc\RequestValidationBundle\DataReceiver\JsonContentDataReceiver;
use Yc\RequestValidationBundle\RequestValidator\RequestValidatorInterface;

abstract class AbstractRequestValidator implements RequestValidatorInterface, DataReceiverInterface
{
    private DataReceiverInterface $dataReceiver;

    public function __construct()
    {
        $this->dataReceiver = new JsonContentDataReceiver();
    }

    public function getGroups(Request $request): array
    {
        return [];
    }

    public function getData(Request $request): mixed
    {
        return $this->dataReceiver->getData($request);
    }

    public function getInvalidRequestResponse(Request $request, ConstraintViolationListInterface $errors): Response
    {
        return new ValidationErrorResponse($errors);
    }
}
