<?php
namespace Framework\Utils;

use Psr\Container\ContainerInterface;

interface StaticallyInstancedInterface {
    
    public static function init(ContainerInterface $container): void;
    
}
