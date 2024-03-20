<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use RuntimeException;

// Custom Exceptions
use Illuminate\Http\Exceptions\PostTooLargeException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
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
     * @return \Illuminate\Http\Response
     */
     public function render($request, Exception $exception) {
       if(config('app.debug')) {
         if ($exception instanceof PostTooLargeException) {
           return response(trans('exception.PostTooLargeException',
                           ['tamanio' => ini_get('post_max_size')]), 422);
         } else {
           return parent::render($request, $exception);
         }
       }

       if($exception instanceof AuthenticationException) {
         return redirect()->guest(route('ingresar'));
       }

       if(!$request->expectsJson()) {
         if($exception instanceof NotFoundHttpException) {
           if($exception->getStatusCode() == 404)
              return parent::render($request, $exception);
           else
              return response()->view('errors.custom', ['mensaje' => 'Ha ocurrido un error'], 500);
         } elseif ($exception instanceof PostTooLargeException) {
             return response(trans('exception.PostTooLargeException',
                             ['tamanio' => ini_get('post_max_size')]), 422);
         } elseif($exception instanceof HttpException) {
            if($exception->getStatusCode() == 403)
              return response()->view('errors.403', [], 403);
            else
              return response()->view('errors.custom', ['mensaje' => trans('exception.ha_ocurrido_un_error')], 500);
         } elseif($exception instanceof RuntimeException) {
           return response()->view('errors.custom', ['mensaje' => trans('exception.ha_ocurrido_un_error')], 500);
            return false;
         } else {
           return response()->view('errors.custom', ['mensaje' => trans('exception.ha_ocurrido_un_error')], 500);
         }
         return parent::render($request, $exception);
       }
     }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
      // dd(0);
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('inicio'));
    }
}
?>
