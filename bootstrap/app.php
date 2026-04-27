<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SiswaAccessWindowMiddleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'guest' => RedirectIfAuthenticated::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'siswa.access.window' => SiswaAccessWindowMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda sudah berakhir. Silakan muat ulang halaman dan coba lagi.',
                ], 419);
            }

            if ($request->isMethod('get')) {
                return redirect()
                    ->route('login')
                    ->withErrors(['session' => 'Sesi Anda sudah berakhir. Silakan login kembali.']);
            }

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['session' => 'Token keamanan tidak valid atau sesi berakhir. Silakan coba lagi.']);
        });
    })->create();
