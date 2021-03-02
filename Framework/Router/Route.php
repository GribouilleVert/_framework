<?php
namespace Framework\Router;

use AltoRouter;
use Framework\Router\Exceptions\RouterException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class Route {

    private string $name;

    private string $method;

    private string $path;

    /**
     * @var mixed
     */
    private $target;

    /**
     * @var MiddlewareInterface[]
     */
    public array $middlewares = [];

    public function __construct(string $name, string $method, string $path, $target)
    {
        $this->name = $name;
        $this->method = $method;
        $this->path = $path;
        $this->target = $target;
    }

    public function addOntoInternalRouter(AltoRouter $router): void
    {
        $router->map($this->method, $this->path, $this->target, $this->name);
    }


    /**
     * @param string|MiddlewareInterface $middleware
     */
    public function addMiddleware($middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @param string[]|MiddlewareInterface[] $middlewares
     */
    public function addMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) $this->addMiddleware($middleware);
    }

}