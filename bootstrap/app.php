<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if (file_exists(database_path('database.sqlite'))) {
    $source = database_path('database.sqlite');
    $destination = '/tmp/database.sqlite';

    // Mueve la base de datos a /tmp si no existe ya
    if (!file_exists($destination)) {
        copy($source, $destination);
    }

    // Asegúrate de que Laravel use el archivo en /tmp
    putenv('DB_DATABASE=' . $destination);
    config(['database.connections.sqlite.database' => $destination]);
}
    