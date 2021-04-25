<?php
namespace Framework\Renderer;

use Framework\Router\Router;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class RouterExtension implements ExtensionInterface {

    /**
     * @var Router
     */
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('route', [$this->router, 'generateUri']);
        $engine->registerFunction('set_method', [$this, 'methodInput']);
    }

    public function methodInput(string $method): string
    {
        return <<<HTML
        <input type="hidden" hidden style="display: none;" name="_method" value="$method">
        HTML;

    }
}
