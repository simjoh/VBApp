<?php

use Slim\App;

return function (App $app) {
    // empty

    $app->get('/ping', \App\Action\Ping\PingAction::class)->setName('ping');
    $app->get('/', \App\Action\HomeAction::class)->setName('home');

};
