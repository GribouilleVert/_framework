<?php

use Laminas\Diactoros\Response\HtmlResponse;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

/******************\
|       APP        |
\******************/
$app = new Framework\App;
//---------------------

/*******************\
|      ROUTING      |
|   & MIDDLEWARES   |
\*******************/
$router = $app->getContainer()->get(Framework\Router\Router::class);

$router->addMiddlewares([
    Middlewares\Whoops::class,
    Framework\Middlewares\HttpsMiddleware::class,
    Framework\Middlewares\TrailingSlashMiddleware::class,
    Framework\Middlewares\MethodDetectorMiddleware::class,
    Framework\Middlewares\CsrfMiddleware::class,
    Framework\Middlewares\RequestPopulatorMiddleware::class,
]);

$router->map('GET', '/', 'index', [App\Controllers\DefaultController::class, 'index']);

# 404
$router->addPostDispatchMiddleware(App\Middlewares\NotFoundMiddleware::class);

//---------------------

/******************\
|    EXECUTION     |
\******************/
$request = Framework\App::fromGlobals();
$response = $app->run($request, $router);
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);

//---------------------
