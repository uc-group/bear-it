# Validating request data

The validation of the request data follows the process workflow:

1) encoding the body from json
2) validating the sent data
3) if validation has failed, respond with appropriate response.

To unify the response there is available `App\Http\Response\ValidationErrorResponse`.

But there is more. To remove the boiler plate, there is an api DataValidator.
To use it you have to add a class implementing the `App\Api\DataValidator\DataValidatorInterface`.
It allows you to define constraints for the data and the validation groups.
Also you can modify the data in the `getData(Request, $data)` method. It will be set
as an $apiData attribute in the request.
If the request data does not met the constraints, the `ValidationErrorResponse` will be sent.

This mechanism works by convention:
1) implement the DataValidatorInterface
2) The class name must be like `Some\Awesome\Namespace\{Controller}\{Action}DataValidator`
3) This api validator will be used on route named: `api_{controller}_{action}`.

If you don't like this convention you can manually point the proper route
by adding service tag with specified route name, i.e.:

```
services:
    Some\Other\Awesome\ValidatorClass:
        tags:
            - { name: 'bearit.api_data_validator', route: 'some_different_route' }
``` 