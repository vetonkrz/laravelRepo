<?php

use App\Http\Middleware\isAdminMiddleware;
use App\Mail\PostCountMail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use App\Http\Middleware\CanViewPostMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'can-view-post' => CanViewPostMiddleware::class,
            'isAdmin' => isAdminMiddleware::class,
        ]);
    })
    ->withSchedule(function(Schedule $schedule){
        $schedule->call(function(){
            Mail::to('admin@example.com')->send(new PostCountMail());
        })->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
