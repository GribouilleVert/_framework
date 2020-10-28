<?php
namespace Framework;

use Psr\Container\ContainerInterface;

/**
 * @param string[] $classes
 * @param ContainerInterface $container
 * @return object[]
 */
function array_resolve(array $classes, ContainerInterface $container): array
{
    //Filter non classes out
    $classes = array_filter($classes, function ($className) {
        return !is_string($className) OR class_exists($className);
    });

    //Instantiate classes
    $instances = array_map(function (string $className) use ($container) {
        return $container->get($className);
    }, $classes);

    return $instances;
}
