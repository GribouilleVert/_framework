<?php
namespace Framework\Utils;

use Psr\Container\ContainerInterface;

class StaticInstancier {

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var string[]
     */
    private array $classList = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function initClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('The class name doesn\'t corresponsd to any known class.');
        }

        $implementations = class_implements($className);
        if (!in_array(StaticallyInstancedInterface::class, $implementations)) {
            throw new \InvalidArgumentException('The class name doesn\'t correspond to a class wich implements ' . StaticallyInstancedInterface::class . '.');
        }

        if (in_array($className, $this->classList)) {
            return; //Déjà instanciée
        }

        call_user_func([$className, 'init'], $this->container);
        $this->classList[] = $className;
    }
}
