<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    const ERROR_DEFINITIONS = [
        400 => [
            'errorCategory' => 'validation',
            'errorCode' => 'formatError',
            'errorDescription' => 'The specified property contents do not conform to the format required for this Property.',
        ],
        401 => [
            'errorCategory' => 'unauthorized',
            'errorCode' => 'AuthnorizeError',
            'errorDescription' => 'The user does not have valid authentication credentials for the target resource',
        ],
        404 => [
            'errorCategory'    => 'Identification',
            'errorCode'        => 'IdentifierError',
            'errorDescription' => 'The requested resource could not be matched on the system with the supplied identifier(s).',
        ],
        500 => [
            'errorCategory'    => 'Internal',
            'errorCode'        => 'GenericError',
            'errorDescription' => 'The request could not be completed due to a non-client related issues that do not constitute complete system unavailability. Examples include software licence issues, unavailability of system configuration information.',
        ],
        503 => [
            'errorCategory'    => 'ServiceUnavailable',
            'errorCode'        => 'GenericError',
            'errorDescription' => 'The service is not currently available. This could be due to network issues, issues with individual components or complete systems outages. Regardless of the cause, the result means that the request cannot be performed.',
        ],
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    protected function prepareJsonResponse($request, Exception $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            ['X-Date' => (new Carbon())->toRfc7231String()],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert the given exception to an array.
     *
     * @param \Exception $e
     * @return array
     * @throws Exception
     */
    protected function convertExceptionToArray(Exception $e)
    {
        $status = $this->isHttpException($e) ? $e->getStatusCode() : 500;
        $content = $this->getErrorDefinitions($status);
        $content['errorDescription'] = $content['errorDescription'] ?: $e->getMessage();

        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : array_merge(
            $content,
            [
                'errorDateTime'    => (new Carbon())->toIso8601ZuluString('millisecond'),
            ]
        );
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Validation\ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $status = 400;
        $errorParameters = [];
        foreach ($exception->errors() as $key => $value) {
            $errorParameters[] = [
                'key'   => $key,
                'value' => $value[0],
            ];
        }

        return response()->json(
            array_merge(
                $this->getErrorDefinitions($status),
                [
                    'errorDateTime'    => (new Carbon())->toIso8601ZuluString('millisecond'),
                    'errorParameters'  => $errorParameters,
                ]
            ),
            $status,
            ['X-Date' => (new Carbon())->toRfc7231String()]
        );
    }

    /**
     * @param $status
     * @return array|mixed
     */
    protected function getErrorDefinitions($status)
    {
        $defaultDefinition = [
            'errorCategory' => '',
            'errorCode' => '',
            'errorDescription' => '',
        ];
        return self::ERROR_DEFINITIONS[$status] ?? $defaultDefinition;
    }
}
