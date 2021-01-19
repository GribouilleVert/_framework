<?php

use League\Route\Strategy\ApplicationStrategy;

return [

    //La stratégie de votre application
    'app.strategy' => \DI\get(ApplicationStrategy::class),

];
