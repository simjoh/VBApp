<?php

use App\Middleware\ApiKeyValidatorMiddleware;
use Slim\App;

return function (App $app) {

    // validate api key for all requests
    $app->add(ApiKeyValidatorMiddleware::class . ':validate');

    // add more endpoints here
    $app->get('/ping', \App\Action\Ping\PingAction::class)->setName('ping');
    $app->get('/', \App\Action\HomeAction::class)->setName('home');

};
