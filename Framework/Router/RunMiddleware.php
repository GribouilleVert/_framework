<?php
namespace Framework\Router;

use Framework\Router\Exceptions\Http\NotFoundException;
use Framework\Router\Exceptions\RouterException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RunMiddleware implements MiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var mixed
     */
    private $target;

    private array $params;

    public function __construct(ContainerInterface $container, $target, array $params)
    {
        $this->container = $container;
        $this->target = $target;
        $this->params = $params;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $callable = $this->resolveCallable($this->target);
        if (is_null($callable)) {
            throw new RouterException("Invalid callable for matched route.");
        }

        return $callable($request, $this->params);
    }

    private function resolveCallable($callable): ?callable
    {
        if (is_string($callable) && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }

        if (is_array($callable) && isset($callable[0]) && is_object($callable[0])) {
            $callable = [$callable[0], $callable[1]];
        }

        if (is_array($callable) && isset($callable[0]) && is_string($callable[0])) {
            $callable = [$this->container->get($callable[0]), $callable[1]];
        }

        if (is_string($callable) && method_exists($callable, '__invoke')) {
            $callable = $this->container->get($callable);
        }

        if (!is_callable($callable)) {
            return null;
        }

        return $callable;
    }
}