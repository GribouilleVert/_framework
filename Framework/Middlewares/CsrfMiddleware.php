<?php
namespace Framework\Middlewares;

use ArrayAccess;
use Exception;
use Framework\Middlewares\Exceptions\CsrfException;
use Framework\Services\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface {

    private string $fieldName;

    private string $sessionKey;

    private string $tokenLimit;

    /**
     * @var ArrayAccess|array
     */
    private $session;

    public function __construct(
        SessionInterface $session,
        int $tokenLimit = 15,
        string $fieldName = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->session = &$session;
        $this->session = &$session;
        $this->fieldName = $fieldName;
        $this->sessionKey = $sessionKey;
        $this->tokenLimit = $tokenLimit;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $params = $request->getParsedBody();
            if (is_null($params)) {
                return $handler->handle($request);
            } elseif (!array_key_exists($this->fieldName, $params)) {
                return $this->reject();
            } else {
                $tokensList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->fieldName], $tokensList)) {
                    $this->useToken($params[$this->fieldName]);
                    return $handler->handle($request);
                } else {
                    return $this->reject();
                }
            }
        }
        return $handler->handle($request);
    }

    /**
     * Génère un token aléatoire cryptographiquement sécurisé
     * @return string
     * @throws \Exception
     */
    public function makeToken(): string
    {
        $token = bin2hex(random_bytes(16));

        $tokensList = $this->session[$this->sessionKey] ?? [];
        $tokensList[] = $token;
        $this->session[$this->sessionKey] = $tokensList;

        $this->limitTokens();
        return $token;
    }

    /**
     * @param $token
     */
    private function useToken($token): void
    {
        $tokensList = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $t !== $token;
        });
        $this->session[$this->sessionKey] = $tokensList;
    }

    private function limitTokens(): void
    {
        $tokensList = $this->session[$this->sessionKey] ?? [];
        if (count($tokensList) > $this->tokenLimit) {
            array_shift($tokensList);
            $this->session[$this->sessionKey] = $tokensList;
        }
    }

    /**
     * Actions a faire en cas de token invalide
     * @throws \Exception
     * @return ResponseInterface
     */
    private function reject(): ResponseInterface
    {
        throw new CsrfException('Unable to verify user request\'s authenticity.');
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
