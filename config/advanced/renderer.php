<?php

use Framework\Renderer\AuthExtension;
use Framework\Renderer\CsrfExtension;
use Framework\Renderer\ManifestExtension;
use Framework\Renderer\NeonExtension;
use Framework\Renderer\RouterExtension;

return [

    'renderer.defaultPath' => 'templates',
    'renderer.additionalPaths' => [],
    'renderer.functions' => [],
    'renderer.extensions' => [
        /***********************\
        |    Vos extensions     |
        \***********************/


        /***********************\
        | Extensions _framework |
        \***********************/
        # Enable only if you have an implement of AuthenticationInterface
        # N'activez que si vous implementez l'AuthenticationInterface
        //\DI\get(AuthExtension::class),

        # Disable if you do not use webpack with manifest
        # Désactivez si vous n'utilisez pas webpack avec un manifeste
        \DI\get(ManifestExtension::class),

        # Generally usefull extensions, should not cause any issues, but you can disable them if tou want
        # Extensions généralement utils qui ne devraient pas poser de problèmes, mais vous pouvez les désactiver si vous le souhaitez
        \DI\get(NeonExtension::class),
        \DI\get(CsrfExtension::class),
        \DI\get(RouterExtension::class),
    ],

    # Used by ManifestExtension
    # Utilisé par la ManifestExtension
    'assets.defaultPath' => 'public/assets',
    'assets.defaultPublicPath' => '/assets',
    'assets.bundledPath' => 'public/dist',
    'assets.bundledPublicPath' => '/dist',
    'assets.manifest' => 'public/dist/manifest.json',

];
