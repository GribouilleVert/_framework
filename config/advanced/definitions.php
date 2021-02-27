<?php

use Framework\Factories\EngineFactory;
use Framework\Services\Session\Lithium;
use Framework\Services\Session\SessionInterface;
use League\Plates\Engine;
use Psr\Container\ContainerInterface;

return [

    PDO::class => function (ContainerInterface $c) {
        $pdo = new PDO(
            "{$c->get('database.type')}:host={$c->get('database.host')};port={$c->get('database.port')};dbname={$c->get('database.dbname')};charset=UTF8",
            $c->get('database.username'),
            $c->get('database.password'),
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $pdo;
    },
    SessionInterface::class => \DI\autowire(Lithium::class),
    Engine::class => \DI\factory(EngineFactory::class),

];
