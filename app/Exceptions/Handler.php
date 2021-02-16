<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        $domain = Str::of($request->getHost());
        if ($domain->contains('api.')) {
            if ($e instanceof ValidationException) {
                return $this->convertValidationExceptionToResponse($e, $request);
            }

            $jsonResponse = $this->prepareJsonResponse($request, $e);

            if ($e instanceof ThrottleRequestsException) {
                $jsonResponse->setData([
                    'message' => __('Too Many Requests'),
                    'wait' => $e->getHeaders()['Retry-After']
                ]);
            }

            if ($e instanceof NotFoundHttpException) {
                $jsonResponse->setData([
                    'message' => __('Not Found')
                ]);
            }

            if ($e instanceof AuthenticationException) {
                $jsonResponse->setData([
                    'message' => __('Unauthenticated')
                ])->setStatusCode(401);
            }

            return $jsonResponse;
        }

        if ($domain->contains('admin.')) {
            inertia()->setRootView('admin::app');

            if (in_array($response->status(), [404, 403])) {
                return inertia()->render('Error', [
                    'status' => $response->status(),
                    'message' => $response->status() === 404 ? 'Страница не найдена.' : 'Доступ запрещен.',
                    'metaInfo' => ['title' => $response->status()]
                ])
                    ->toResponse($request)
                    ->setStatusCode($response->status());
            }

            if (!app()->environment('local') && in_array($response->status(), [500, 503])) {
                return Inertia::render('Error', [
                    'status' => $response->status(),
                    'message' => $e->getMessage()
                ])
                    ->toResponse($request)
                    ->setStatusCode($response->status());
            }

            if ($response->status() === 419) {
                return back()->with([
                    'error' => 'Страница устарела, перезагрузите и попробуйте еще раз.'
                ]);
            }
        }


        return $response;
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'message' => __('validation.invalid'),
            'errors' => $exception->errors(),
        ], $exception->status);
    }
}
