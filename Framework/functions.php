<?php
namespace Framework;

use DateTime;
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

/**
 * Transforme une string ou un int en Datetime
 *
 * @param mixed $source La source de la date, les int (timestamps unix) les strings et les DateTime  sont acceptés.
 * @return DateTime|null Null si la date n'a pu être détectée
 */
function detect_date($source): ?DateTime
{
    if (is_int($source)) {
        return (new \DateTime())->setTimestamp($source);
    } elseif (is_string($source)) {
        return new \DateTime($source);
    } elseif ($source instanceof \DateTime) {
        return $source;
    }
    return null;
}
