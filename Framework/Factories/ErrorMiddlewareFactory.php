<?php
namespace Framework\Factories;

use Exception;
use Framework\Guard\AuthenticationInterface;
use Middlewares\Whoops;
use Psr\Container\ContainerInterface;
use Sentry\State\Scope;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use function Sentry\captureException;
use function Sentry\configureScope;
use function Sentry\init;
use const PRODUCTION;
use const SENTRY_ALL;
use const SENTRY_DSN;

class ErrorMiddlewareFactory {

    public static function make(ContainerInterface $container)
    {
        $whoops = new Run();

        if (is_string(SENTRY_DSN)) {
            if (!SENTRY_ALL) {
                init([
                    'dsn' => SENTRY_DSN,
                    'capture_silenced_errors' => true,
                    'environment' => ENV,
                    'release' => RELEASE,
                ]);
            }

            if ($container->has(AuthenticationInterface::class)) {
                $authentification = $container->get(AuthenticationInterface::class);
                if ($authentification->isLogged()) {
                    $user = $authentification->getUser();
                    configureScope(function (Scope $scope) use ($user): void {
                        $scope->setUser([
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'email' => $user->email ?? null,
                        ]);
                    });
                }
            }

            $whoops->appendHandler(new CallbackHandler(function (\Throwable $error) {
                captureException($error);
            }));
        }

        if (!PRODUCTION) {
            $whoops->appendHandler(new PrettyPageHandler);
        } else {
            $whoops->appendHandler(new CallbackHandler(function () use ($container) {
                self::productionErrorHandler($container);
            }));
        }

        $whoops->register();

        return new Whoops($whoops);
    }

    public static function productionErrorHandler(ContainerInterface $container): void
    {
        ob_end_flush();
        http_response_code(500);
        try {
            require 'templates/errors/500.php';
        } catch (Exception $e) {
            echo <<<HTML
            <!doctype html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                     <meta name="viewport" content="width=device-width, initial-scale=1.0">
                     <meta http-equiv="X-UA-Compatible" content="ie=edge">
                     <title>Errur 500</title>
                </head>
                <body>
                    <h1>Erreur 500</h1>
                    <p>Une erreur est survenue lors du chargement de cette page.</p>
                    <p>
                         <b>De plus, une erreur est survenue lors de l'affichage de la page d'erreur:</b>
                         <br>
                         {$e->__toString()}                
                    </p>
                </body>
            </html>
            HTML;
        }
        die;
    }

}
