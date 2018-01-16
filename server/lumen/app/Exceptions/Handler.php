<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $response = parent::render($request, $e);

        if ($request->expectsJson()) {
            if ($e instanceof ModelNotFoundException) {
                $e = new NotFoundHttpException($e->getMessage(), $e);
            } elseif ($e instanceof AuthorizationException) {
                $e = new HttpException(403, $e->getMessage());
            }

            $message = ['message' => $e->getMessage()];

            if (env('APP_ENV') == 'local') {
                $message += [
                    'code'      => $e->getCode(),
                    'exception' => class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage(),
                ];

                // Sometimes the trace is recursive, and can't be json-serialized
                $trace = $e->getTrace();
                json_encode($trace);
                if (!json_last_error()) {
                    $message['trace'] = $trace;
                }
            }

            $status = method_exists($e, 'getStatusCode') && is_callable([$e, 'getStatusCode']) ?
                    $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

            $response = response()->json($message, $status, $response->headers->all());
            $response->exception = $e;
        }

        return $response;
    }
}
