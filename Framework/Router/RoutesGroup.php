<?php
namespace Framework\Router;

use AltoRouter;
use Framework\Router\Exceptions\RouterException;
use Psr\Http\Server\MiddlewareInterface;

class RoutesGroup {

    private string $name;

    private string $path;

    /**
     * @var MiddlewareInterface[]
     */
    public array $middlewares = [];

    /**
     * @var Route|RoutesGroup[]
     */
    private array $routes = [];

    private AltoRouter $internalRouter;

    public function __construct(string $name, string $path, AltoRouter $internalRouter)
    {
        $this->name = $name;
        $this->path = $path;
        $this->internalRouter = $internalRouter;
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

    /**
     * @param string $methods
     * @param string $path
     * @param string $name
     * @param callable $callable
     * @return Route
     * @throws RouterException
     */
    public function map(string $methods, string $path, string $name, $callable): Route
    {
        if (str_contains($name, '/')) {
            throw new RouterException("Routes cannot have `/` in their names");
        }

        $route = new Route($this->name . '/' . $name, $methods, $this->path . $path, $callable);
        $route->addOntoInternalRouter($this->internalRouter);
        $this->routes[$name] = $route;
        return $route;
    }

    public function group(string $name, string $path): RoutesGroup
    {
        if (str_contains($name, '.')) {
            throw new RouterException("RoutesGroups cannot have `/` in their names");
        }

        $route = new RoutesGroup($this->name . '/' . $name, $this->path . $path, $this->internalRouter);
        $this->routes[$name] = $route;
        return $route;
    }

    public function getRoute(string $name): ?object
    {
        return $this->routes[$name] ?? null;
    }

}