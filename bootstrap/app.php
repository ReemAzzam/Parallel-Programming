<?php

//use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\PerformanceMonitor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // AOP - Performance Monitoring (Applied on all requests)
        $middleware->append(PerformanceMonitor::class);


       // $middleware->alias([
          //  'CheckUser' => CheckUserRole::class,
           // 'CheckUser' => CheckUserRole::class,
        //]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
