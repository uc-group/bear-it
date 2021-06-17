<?php

namespace App\EventListener;

use App\Api\DataValidator\DataValidatorInterface;
use App\Http\Response\ValidationErrorResponse;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiValidatorSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceLocator
     */
    private $apiDataValidators;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ApiValidatorSubscriber constructor.
     * @param ServiceLocator $apiDataValidators
     * @param ValidatorInterface $validator
     */
    public function __construct(ServiceLocator $apiDataValidators, ValidatorInterface $validator)
    {
        $this->apiDataValidators = $apiDataValidators;
        $this->validator = $validator;
    }

    /**
     * @param RequestEvent $event
     */
    public function validateData(RequestEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (!$routeName || !$this->apiDataValidators->has($routeName)) {
            return;
        }

        /** @var DataValidatorInterface $validator */
        $validator = $this->apiDataValidators->get($routeName);
        $data = json_decode($request->getContent(), true);
        $errors = $this->validator->validate(
            $data,
            $validator->getConstraint($request),
            $validator->getGroups($request)
        );

        if ($errors->count() > 0) {
            $event->setResponse(new ValidationErrorResponse($errors));
        }

        $request->attributes->add([
            'apiData' => $validator->getData($request, $data)
        ]);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'validateData'
        ];
    }
}