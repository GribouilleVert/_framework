<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$container = (new Framework\Factories\ContainerFactory)();

$strategy = (new League\Route\Strategy\ApplicationStrategy())->setContainer($container);
$router   = (new League\Route\Router)->setStrategy($strategy);

$router->middlewares(Framework\array_resolve([
    Framework\Middlewares\HttpsMiddleware::class,
    Framework\Middlewares\TralingSlashMiddleware::class,
    Framework\Middlewares\MethodDetectorMiddleware::class,
], $container));

/******************\
|      ROUTES      |
\******************/
$router->get('/', [App\Controllers\DefaultController::class, 'index']);


/******************\
|    EXECUTION     |
\******************/
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = $router->dispatch($request);
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
