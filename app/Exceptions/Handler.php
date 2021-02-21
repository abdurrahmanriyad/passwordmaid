<?php

namespace App\Exceptions;

use App\Responses\AjaxResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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


    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            if ($e instanceof ModelNotFoundException) {
                return AjaxResponse::render('error', [], 'Not found!', 404);
            }

            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->view('404');
        }

        return parent::render($request, $e);

    }
}
