<?php
namespace Framework;

use DI\Container;
use DI\ContainerBuilder;
use DI\NotFoundException;
use Exception;
use Framework\Exceptions\SystemException;
use Framework\Factories\ContainerFactory;
use Framework\Factories\StaticInstancierFactory;
use Framework\Middlewares\Internals\FileUploadErrorDetectionMiddleware;
use Framework\Middlewares\Internals\RequestParametersCustomsMiddleware;
use Framework\Router\Router;
use Framework\System\PHPRenderer;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Class App
 * @package TurboPancake
 */
final class App implements RequestHandlerInterface {

    public const VERSION = '1.1';

    private const INTERNAL_MIDDLEWARES = [
        FileUploadErrorDetectionMiddleware::class,
        RequestParametersCustomsMiddleware::class,
    ];

    /**
     * Router de l'application
     * @var Container
     */
    private Container $container;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $container = (new ContainerFactory)();
        StaticInstancierFactory::init($container);
    }

    /**
     * Lance le traitement global - ENTRY POINT
     * @param ServerRequestInterface $request
     * @param Router $router
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request, Router $router): ResponseInterface
    {
        $applicationDetails = [
            'version' => '_framework ' . self::VERSION,
            'container' => get_class($this->container)
        ];

        $this->container->set('_framework.details', $applicationDetails);

        $this->index = 0;
        $this->hasHandledGenericMiddlewares = false;
        $this->middlewares = array_merge(self::INTERNAL_MIDDLEWARES, $router->getMiddlewares());
        $this->runTimeRoute = $router;

        try {
            return $this->handle($request);
        } catch (Exception $e) {
            $this->error(new SystemException($e->getMessage(), SystemException::SEVERITY_HIGH));
        }
    }

    private function runRouting(ServerRequestInterface $request): void
    {
        $this->hasHandledGenericMiddlewares = true;
        $this->middlewares = array_merge($this->middlewares, $this->runTimeRoute->run($request, true));
    }

    private int $index = 0;
    private bool $hasHandledGenericMiddlewares = false;
    private array $middlewares;
    private Router $runTimeRoute;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            if (!$this->hasHandledGenericMiddlewares) {
                $this->runRouting($request);
                return $this->handle($request);
            }
            throw new SystemException('None of the middlewares caught the request.', SystemException::SEVERITY_MEDIUM);
        } elseif ($middleware instanceof MiddlewareInterface) {
            try {
                return $middleware->process($request, $this);
            } catch (SystemException $e) {
                $this->error($e);
            }
        }
    }

    /**
     * @return MiddlewareInterface|null
     * @throws SystemException
     * @throws \DI\DependencyException
     */
    private function getMiddleware(): ?object
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                try {
                    $middleware = $this->container->get($this->middlewares[$this->index]);
                } catch (NotFoundException $exception) {
                    throw new SystemException('The container can\'t find the middleware: ' . $exception->getMessage());
                }
            } elseif ($this->middlewares[$this->index] instanceof MiddlewareInterface) {
                $middleware = $this->middlewares[$this->index];
            } else {
                throw new SystemException('Invalid middleware type, only strings and instances of MiddlewareInterface are accepted.');
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if (!isset($this->container) OR !$this->container instanceof ContainerInterface) {
            $builder = new ContainerBuilder();
            if (PRODUCTION) {
                $builder->enableDefinitionCache();
                $builder->enableCompilation('tmp');
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }

            $files = require 'config.php';
            foreach ($files as $file) {
                $builder->addDefinitions($file);
            }

            try {
                $this->container = $builder->build();
            } catch (\Exception $e) {
                $this->error(new SystemException(
                    'Unable to build container: ' . $e->getMessage(),
                    SystemException::SEVERITY_CRITICAL
                ));
            }
        }

        return $this->container;
    }

    /**
     * Crée un objet ServerRequestInterface à partir des variables globales.
     *
     * @return ServerRequestInterface
     */
    public static function fromGlobals(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }

    /**
     * Affiche une erreur système et assassine le script.
     *
     * @param mixed ...$exceptions
     */
    private function error(...$exceptions)
    {
        $renderer = new PHPRenderer(__DIR__ . '/System/views');
        try {
            echo $renderer->render('error', [
                'exceptions' => $exceptions,
                'details' => $this->container->get('_framework.details'),
            ]);
        } catch (Throwable $e) {
            echo $renderer->render('error-no-container', [
                'exceptions' => $exceptions,
                'details' => [
                    'version' => '_framework ' . self::VERSION
                ]
            ]);
        }
        die;
    }
}