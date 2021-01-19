<?php
namespace Framework\Renderer;

use Framework\Guard\AuthenticationInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class AuthExtension implements ExtensionInterface {

    /**
     * @var AuthenticationInterface
     */
    private AuthenticationInterface $authentication;

    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('is_logged', [$this->authentication, 'isLogged']);
        $engine->registerFunction('current_user', [$this->authentication, 'getUser']);
    }
}
