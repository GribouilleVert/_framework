<?php
namespace Framework\Renderer;

use Framework\Middlewares\CsrfMiddleware;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class CsrfExtension implements ExtensionInterface {

    /**
     * @var CsrfMiddleware
     */
    private CsrfMiddleware $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('csrf', [$this, 'input']);
        $engine->registerFunction('csrf_token', [$this->csrfMiddleware, 'makeToken']);
    }

    public function input()
    {
        return <<<HTML
        <input type="hidden" hidden style="display: none" name="{$this->csrfMiddleware->getFieldName()}" value="{$this->csrfMiddleware->makeToken()}">
        HTML;
    }
}
