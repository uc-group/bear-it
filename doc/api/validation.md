# Validating request data

The validation of the request data follows the process workflow:

1) encoding the body from json
2) validating sent data
3) if validation has failed, the response `App\Http\Response\ValidationErrorResponse` is returned by the controller.

To remove boilerplate, bear-it is using
[youniverse-center/request-validation-bundle](https://github.com/youniverse-center/request-validation-bundle).
