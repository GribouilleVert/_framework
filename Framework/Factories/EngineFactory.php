<?php
namespace Framework\Factories;

use League\Plates\Engine;
use Psr\Container\ContainerInterface;

class EngineFactory {

    public function __invoke(ContainerInterface $container)
    {
        $engine = new Engine($container->get('renderer.defaultPath'));
        foreach ($container->get('renderer.additionalPaths') as $namespace => $path) {
            $engine->addFolder($namespace, $path);
        }

        foreach ($container->get('renderer.functions') as $name => $callable) {
            $engine->registerFunction($name, $callable);
        }

        $engine->loadExtensions($container->get('renderer.extensions'));

        return $engine;
    }

}
