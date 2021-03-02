<?php

use Framework\Exceptions\SystemException;

/**
 * @var SystemException[] $exceptions
 * @var string[] $details
 * @var string[] $modules
 * @var string[] $loaded_modules
 * @var string[] $middlewares
 */
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>_framework Error</title>

        <link rel="stylesheet" href="https://turbopancake.s3.fr-par.scw.cloud/assets/css/style.css" type="text/css">
        <link rel="icon" href="https://turbopancake.s3.fr-par.scw.cloud/framework/favicon.png" type="image/png">
    </head>
    <body>
        <nav>
            <img src="https://turbopancake.s3.fr-par.scw.cloud/framework/logo-long.png" alt="Logo">
        </nav>
        <main>
            <div class="error-infos">
                <div class="title">_framework Error !</div>
                <div class="errors">
                    <?php foreach ($exceptions as $exception): ?>
                        <div class="error">
                            <div class="details">
                                <span class="severity <?=$exception->getSeverityText()?>"><?=$exception->getSeverityText()?></span>
                                <span class="location"><?=$exception->getFile()?>:<?=$exception->getLine()?></span>
                            </div>
                            <p class="message <?=$exception->getSeverityText()?>"><?=$exception->getMessage()?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="framework-infos">
                    <b><?=$details['version']?></b>
                    <i><?=$details['container']?></i>
                </div>
            </div>
        </main>
    </body>
</html>