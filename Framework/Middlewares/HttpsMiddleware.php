<?php
namespace Framework\Middlewares;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use const PRODUCTION;

class HttpsMiddleware implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $scheme = $request->getUri()->getScheme();
        if ($scheme !== 'https' AND PRODUCTION) {
            return new RedirectResponse((string)$request->getUri()->withScheme('https'), 301);
        }
        return $handler->handle($request);
    }

}
