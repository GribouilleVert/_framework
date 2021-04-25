<?php
namespace Framework\Middlewares\Internals;

use Framework\Errors\Exceptions\SystemException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class RequestParametersCustomsMiddleware implements MiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws SystemException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        //Query parameters
        $allowedPostArray = $this->getAllowedArrayKey('customs.get.allowedArrayKeys', 'Get');
        $newQueryParams = $this->filterParams($request->getQueryParams(), $allowedPostArray);

        //Body parameters
        $allowedPostArray = $this->getAllowedArrayKey('customs.post.allowedArrayKeys', 'Post');
        $newBodyParams = $this->filterParams($request->getParsedBody(), $allowedPostArray);

        return $handler->handle($request
            ->withQueryParams($newQueryParams)
            ->withParsedBody($newBodyParams));
    }

    private function filterParams(array $input, array $safeKeys): array
    {
        $newParams = [];
        foreach ($input as $key => $value) {
            if (is_array($value) AND !in_array($key, $safeKeys)) {
                continue;
            }

            try {
                $value = strval($value);
            } catch (Throwable $t) {
                continue;
            }

            $newParams[$key] = $value;
        }
        return $newParams;
    }

    private function getAllowedArrayKey(string $containerKey, string $errorHint): array
    {
        if ($this->container->has($containerKey)) {
            $_ = $this->container->get($containerKey);
            if (!is_array($_)) {
                throw new SystemException($errorHint . ' customs config is invalid: "customs.post.allowedArrayKeys" is not an array.', SystemException::SEVERITY_LOW);
            }

            foreach ($_ as $allowedGetKey) {
                if (!is_string($allowedGetKey)) {
                    throw new SystemException($errorHint . ' customs config is invalid: "customs.post.allowedArrayKeys" contains non string values.', SystemException::SEVERITY_LOW);
                }
            }

            return $_;
        }
        return [];
    }
}
