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
        $errorCode = $e->getCode() > 0 ? $e->getCode() : 500;

        if ($e instanceof NotFoundHttpException) {
            flash()->error(trans('permissions.notfound'));
            return redirect('/data');
        }
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }
        if ($e instanceof PermissionDeniedException) {
            flash()->error(trans('permissions.unauthorized'));
            return redirect('data');
        }
        if ($e instanceof RoleDeniedException) {
            flash()->error(trans('permissions.unauthorized'));
            return redirect('data');
        }
        if (!env('APP_DEBUG')) {
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
