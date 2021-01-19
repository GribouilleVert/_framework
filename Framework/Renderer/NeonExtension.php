<?php
namespace Framework\Renderer;

use Framework\Services\Neon;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class NeonExtension implements ExtensionInterface {

    /**
     * @var Neon
     */
    private Neon $neon;

    public function __construct(Neon $neon)
    {
        $this->neon = $neon;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('get_flashs', [$this->neon, 'get']);
    }
}
