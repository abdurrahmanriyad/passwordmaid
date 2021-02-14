<?php

namespace App\Exceptions;

use App\Responses\AjaxResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
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
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->wantsJson()) {
            if ($exception instanceof ModelNotFoundException) {
                return AjaxResponse::render('error', [], 'Not found!', 404);
            }

            return AjaxResponse::render('error', [], $exception->getMessage(), 400);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->view('404');
        }

//        if ($exception instanceof HttpExceptionInterface) {
//            return response()->view('exception', [
//                'status' => $exception->getStatusCode()
//            ]);
//        }

        return parent::render($request, $exception);

    }
}