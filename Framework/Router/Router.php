<?php

namespace Framework\Router;

use AltoRouter;
use Framework\Router\Exceptions\RouterException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class Router {

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var MiddlewareInterface[]
     */
    private array $middlewares = [];

    /**
     * @var MiddlewareInterface[]
     */
    private array $postDispatchMiddlewares = [];

    /**
     * @var Route|RoutesGroup[]
     */
    private array $routes = [];

    private AltoRouter $internalRouter;

    public function __construct(ContainerInterface $container, string $basePath = '', array $matchTypes = [])
    {
        $this->container = $container;
        $this->internalRouter = new AltoRouter([], $basePath, $matchTypes);
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
     * @param string|MiddlewareInterface $middleware
     */
    public function addPostDispatchMiddleware($middleware): void
    {
        $this->postDispatchMiddlewares[] = $middleware;
    }

    /**
     * @param string[]|MiddlewareInterface[] $middlewares
     */
    public function addPostDispatchMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) $this->addPostDispatchMiddleware($middleware);
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
        if (str_contains($name, '.')) {
            throw new RouterException("Routes cannot have `/` in their names");
        }

        $route = new Route($name, $methods, $path, $callable);
        $route->addOntoInternalRouter($this->internalRouter);
        $this->routes[$name] = $route;
        return $route;
    }

    public function group(string $name, string $path): RoutesGroup
    {
        if (str_contains($name, '/')) {
            throw new RouterException("RoutesGroups cannot have `/` in their names");
        }

        $route = new RoutesGroup($name, $path, $this->internalRouter);
        $this->routes[$name] = $route;
        return $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @return MiddlewareInterface[]|string[]
     */
    public function run(ServerRequestInterface $request): array
    {
        $match = $this->internalRouter->match();
        $route = null;
        if ($match === false) $route = null;
        elseif (str_contains($match['name'], '/')) {
            $parts = explode('/', $match['name']);
            $group = $this;
            foreach ($parts as $part) {
                $group = $group->getRoute($part);
                if ($group instanceof Route) {
                    $route = $group;
                }
            }
        } else {
            $route = $this->routes[$match['name']];
        }

        if ($route instanceof Route) {
            $this->addMiddlewares($route->middlewares);
            $this->addMiddleware(new RunMiddleware(
                $this->container,
                $match['target'],
                $match['params']
            ));
        }
        $this->addMiddlewares($this->postDispatchMiddlewares);

        return $this->middlewares;
    }

    public function getRoute(string $name): ?object
    {
        return $this->routes[$name] ?? null;
    }
}