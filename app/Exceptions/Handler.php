<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $this->convertValidationExceptionToResponse($exception, $request);
        } elseif ($exception instanceof ModelNotFoundException) {
            return $this->errorResponse('No existe ninguna instancia con el id especificado', 404);
        } elseif ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        } elseif ($exception instanceof JWTException) {
            return $this->unauthenticated($request, $exception);
        } elseif ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('No se encontro la URL especificada', 404);
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('El método especificado no es valido', 405);
        } elseif ($exception instanceof HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        } elseif ($exception instanceof QueryException) {
            $codigo = $exception->errorInfo[1];

            if ($codigo == 1451) {
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso por estar relacionado
                con otro', 409);
            }
        }
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }
        return $this->errorResponse('Error inesperado por favor consulte al administrador del sistema.', 500);
    }
}
