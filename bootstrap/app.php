<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {$middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {$exceptions->render(function (TokenMismatchException $e,$request) {
            if ($request->is('logout')) {
                return redirect()->route('home');
            }
            return redirect()->route('login')->with('error', 'Sesi formulir berakhir, silakan ulangi kembali.');
        });

        $exceptions->render(function (HttpException $e,$request) {
            if ($e->getStatusCode() === 419) {
                if ($request->is('logout')) {
                    return redirect()->route('home');
                }
                return redirect()->route('login')->with('error', 'Sesi formulir berakhir, silakan ulangi kembali.');
            }
        });
    })->create();