<?php

use Framework\Renderer\AuthExtension;
use Framework\Renderer\ManifestExtension;
use Framework\Renderer\NeonExtension;

return [

    'renderer.defaultPath' => 'templates',
    'renderer.additionalPaths' => [],
    'renderer.functions' => [],
    'renderer.extensions' => [
//        \DI\get(AuthExtension::class), #Enable only if you have an implement of AuthenticationInterface
        \DI\get(NeonExtension::class),
        \DI\get(ManifestExtension::class),
    ],

    'assets.defaultPath' => 'public/assets',
    'assets.defaultPublicPath' => '/assets',
    'assets.bundledPath' => 'public/dist',
    'assets.bundledPublicPath' => '/dist',
    'assets.manifest' => 'public/dist/manifest.json',

];
