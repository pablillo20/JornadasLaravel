<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Los middleware globales de la aplicación.
     *
     * @var array
     */
    protected $middleware = [
        // Aquí van los middleware globales...
    ];

    /**
     * Los grupos de middleware de la aplicación.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // Los middleware específicos para web...
        ],

        'api' => [
            // Los middleware específicos para API...
        ],
    ];

    /**
     * Los middleware de ruta específicos de la aplicación.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\IsAdmin::class, 
    ];    
}
