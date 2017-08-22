<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Bican\Roles\Exceptions\PermissionDeniedException;
use Bican\Roles\Exceptions\RoleDeniedException;
use Session;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        //HttpException::class,
        //ModelNotFoundException::class,
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
        return parent::report($e);
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
        if (!env('APP_DEBUG')) {
            $errorCode = $e->getCode();

            if ($e instanceof NotFoundHttpException) {
                $errorCode = 404;
            }
            if ($e instanceof ModelNotFoundException) {
                $errorCode = 404;
            }
            if ($e instanceof PermissionDeniedException) {
                $errorCode = 403;
            }
            if ($e instanceof RoleDeniedException) {
                $errorCode = 403;
            }

            $errorCode = $errorCode > 0 ? $errorCode : 500;

            // if user is logged in
            if (Session::has('tyreapi') && isset(Session::get('tyreapi')['access_token'])) {
                return response()->view('errors.generic', ['errorCode' => $errorCode]);
            }
            else {
                return response()->view('errors.500', ['errorCode' => $errorCode]);
            }

        }

        return parent::render($request, $e);
    }
}
