<?php
namespace Framework\Factories;

use Framework\Utils\StaticInstancier;
use Psr\Container\ContainerInterface;

class StaticInstancierFactory {

    public static function init(ContainerInterface $container): void
    {
        $staticInstancier = $container->get(StaticInstancier::class);
        $classes = $container->get('staticallyInstancedClassed');
        foreach ($classes as $class) {
            $staticInstancier->initClass($class);
        }
    }

}
