<?php

use Laminas\Diactoros\Response\HtmlResponse;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

/******************\
|    CONTAINER     |
\******************/
$container = (new Framework\Factories\ContainerFactory)();
Framework\Factories\StaticInstancierFactory::init($container);
//---------------------

/*******************\
|      ROUTING      |
|   & MIDDLEWARES   |
\*******************/
$router = $container->get(Framework\Router\Router::class);

$router->addMiddlewares([
    Middlewares\Whoops::class,
    Framework\Middlewares\HttpsMiddleware::class,
    Framework\Middlewares\TrailingSlashMiddleware::class,
    Framework\Middlewares\MethodDetectorMiddleware::class,
]);

$router->map('GET', '/', 'index', [App\Controllers\DefaultController::class, 'index']);

# 404
$router->addPostDispatchMiddleware(App\Middlewares\NotFoundMiddleware::class);

//---------------------

/******************\
|    EXECUTION     |
\******************/
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = $router->run($request);
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);

//---------------------
