<?php
namespace Framework\Factories;

use Framework\Middlewares\RequestInstancier\RequestInstancier;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestInstancierFactory {

    public static function init(ContainerInterface $container, ServerRequestInterface $request): void
    {
        $requestInstancier = $container->get(RequestInstancier::class);
        $requestInstancier->setRequest($request);
        $classes = $container->get('requestInstancedClasses');
        foreach ($classes as $class) {
            $requestInstancier->initClass($class);
        }
    }

}
