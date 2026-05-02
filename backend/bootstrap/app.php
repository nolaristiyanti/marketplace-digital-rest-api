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
            'ensure.seller' => \App\Http\Middleware\EnsureSeller::class, // cek role = seller
            'ensure.owner' => \App\Http\Middleware\EnsureProductOwner::class, // cek produk milik user
            'ensure.admin' => \App\Http\Middleware\EnsureAdmin::class, // cek role = admin
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*')) {
                return null;
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e): JsonResponse {
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Route tidak ditemukan'
            ], 404);
        });

        $exceptions->render(function (ValidationException $e): JsonResponse {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 400);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request){
            if($request->is('api/*')){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Silakan login terlebih dahulu',
                ], 401);
            }
        });

        // $exceptions->render(function (\Throwable $e): JsonResponse {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Terjadi kesalahan pada server'
        //     ], 500);
        // });
        $exceptions->render(function (\Throwable $e) {
            return null;
        });
    })->create();
