<?php
namespace Framework\Middlewares\RequestInstancier;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestInstancier {
    /**
     * @var string[]
     */
    private array $classList = [];

    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }


    public function initClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('The class name doesn\'t correspond to any known class.');
        }

        $implementations = class_implements($className);
        if (!in_array(RequestInstancedInterface::class, $implementations)) {
            throw new \InvalidArgumentException('The class name doesn\'t correspond to a class wich implements ' . RequestInstancedInterface::class . '.');
        }

        if (in_array($className, $this->classList)) {
            return; //Déjà instanciée
        }

        call_user_func([$className, 'setRequest'], $this->request);
        $this->classList[] = $className;
    }
}
