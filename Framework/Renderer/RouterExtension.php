<?php
namespace Framework\Renderer;

use Framework\Middlewares\RequestInstancier\RequestInstancedInterface;
use Framework\Router\Router;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouterExtension implements ExtensionInterface, RequestInstancedInterface {

    /**
     * @var Router
     */
    private Router $router;

    private static ServerRequestInterface $request;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public static function setRequest(ServerRequestInterface $request): void
    {
        self::$request = $request;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('route', [$this->router, 'generateUri']);
        $engine->registerFunction('route_match_path', [$this, 'isCurrentRoute']);
        $engine->registerFunction('route_match_path_start', [$this, 'isCurrentRouteStart']);
        $engine->registerFunction('set_method', [$this, 'methodInput']);
    }

    public function isCurrentRoute(string $route, array $arguments = []): bool
    {
        $route = $this->router->generateUri($route, $arguments);
        return $this->getCurrentUri()->getPath() === $route->getPath();
    }

    /**
     * @param $uri string|UriInterface
     * @return bool
     */
    public function isCurrentRouteStart($uri): bool
    {
        $path = ($uri instanceof UriInterface) ? $uri->getPath() : (string)$uri;
        return str_starts_with($this->getCurrentUri()->getPath(), $path);
    }

    public function methodInput(string $method): string
    {
        return <<<HTML
        <input type="hidden" hidden style="display: none;" name="_method" value="$method">
        HTML;

    }

    private function getCurrentUri(): ?UriInterface
    {
        if (self::$request === null) return null;
        return self::$request->getUri();
    }
}
