<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

/******************\
|    CONTAINER     |
\******************/
$container = (new Framework\Factories\ContainerFactory)();
Framework\Factories\StaticInstancierFactory::init($container);
//---------------------

$strategy = (new League\Route\Strategy\ApplicationStrategy())->setContainer($container);
$router   = (new League\Route\Router)->setStrategy($strategy);

/*******************\
|      ROUTING      |
|   & MIDDLEWARES   |
\*******************/
$router->middlewares(Framework\array_resolve([
    Framework\Middlewares\HttpsMiddleware::class,
    Framework\Middlewares\TralingSlashMiddleware::class,
    Framework\Middlewares\MethodDetectorMiddleware::class,
], $container));

$router->get('/', [App\Controllers\DefaultController::class, 'index']);

//---------------------

/******************\
|    EXECUTION     |
\******************/
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = $router->dispatch($request);
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);

//---------------------
