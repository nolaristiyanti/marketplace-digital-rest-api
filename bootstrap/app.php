<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureSeller;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensure.seller' => EnsureSeller::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e): JsonResponse {
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => 'Route Tidak Ditemukan'
            ], 404);
        });

        $exceptions->render(function (ValidationException $e): JsonResponse {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $e->errors()
            ], 422);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request){
            if($request->is('api/*')){
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu',
                ], 401);
            }
        });
    })->create();
