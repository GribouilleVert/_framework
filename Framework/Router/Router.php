<?php

namespace Framework\Router;

use AltoRouter;
use Framework\Router\Exceptions\RouterException;
use Laminas\Diactoros\Uri;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
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

    public function addMatchTypes(array $types): void
    {
        $this->internalRouter->addMatchTypes($types);
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
        if (str_contains($name, '/')) {
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
     * @param bool $runTimeMiddlewaresOnly
     * @return MiddlewareInterface[]|string[]
     */
    public function run(ServerRequestInterface $request, bool $runTimeMiddlewaresOnly = false): array
    {
        $match = $this->internalRouter->match($request->getUri()->getPath(), $request->getMethod());
        $route = null;
        $runTimeMiddlewares = [];
        if ($match === false) $route = null;
        elseif (str_contains($match['name'], '/')) {
            $parts = explode('/', $match['name']);
            $group = $this;
            foreach ($parts as $part) {
                $group = $group->getRoute($part);
                if ($group instanceof Route) {
                    $route = $group;
                } elseif ($group instanceof RoutesGroup) {
                    if ($runTimeMiddlewaresOnly)
                        $runTimeMiddlewares = array_merge($runTimeMiddlewares, $group->middlewares);
                    else
                        $this->addMiddlewares($group->middlewares);
                }
            }
        } else {
            $route = $this->routes[$match['name']];
        }

        if ($route instanceof Route) {
            $runMiddleware = new RunMiddleware(
                $this->container,
                $match['target'],
                $match['params']
            );
            if ($runTimeMiddlewaresOnly) {
                $runTimeMiddlewares = array_merge($runTimeMiddlewares, $route->middlewares, [$runMiddleware]);
            } else {
                $this->addMiddlewares($route->middlewares);
                $this->addMiddleware($runMiddleware);
            }
        }
        if ($runTimeMiddlewaresOnly)
            $runTimeMiddlewares = array_merge($runTimeMiddlewares, $this->postDispatchMiddlewares);
        else
            $this->addMiddlewares($this->postDispatchMiddlewares);

        return $runTimeMiddlewaresOnly ? $runTimeMiddlewares : $this->middlewares;
    }

    public function getRoute(string $name): ?object
    {
        return $this->routes[$name] ?? null;
    }

    public function generateUri(string $name, array $parameters = [], array $query = []): UriInterface
    {
        $uri = new Uri($this->internalRouter->generate($name, $parameters));
        if (!empty($query)) {
            $uri = $uri->withQuery(http_build_query($query));
        }
        return $uri;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}