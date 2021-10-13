<?php

use Slim\App;

return function (App $app) {
    // empty

    $app->get('/', \App\Action\HomeAction::class)->setName('home');


};
