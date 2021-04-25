<?php
namespace Framework\Middlewares\RequestInstancier;

use Psr\Http\Message\ServerRequestInterface;

interface RequestInstancedInterface {
    
    public static function setRequest(ServerRequestInterface $request): void;
    
}
