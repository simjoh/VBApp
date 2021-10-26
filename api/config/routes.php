<?php

use App\Middleware\ApiKeyValidatorMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // validate api key for all requests
    $app->add(ApiKeyValidatorMiddleware::class . ':validate');

    // add more endpoints here
    $app->post('/login', \App\Action\Login\LoginAction::class)->setName('login');

    $app->get('/ping', \App\Action\Ping\PingAction::class)->setName('ping');


     //$app->get('/bla/bla/bla', \App\Action\HomeAction::class)->setName('home');




    // User route group
    $app->group('/api', function(RouteCollectorProxy $apps) use ($app) {


        // place istället för site?
        $app->get('/sites', \App\Action\Site\SitesAction::class . ':allSites');
        $app->get('/site/{siteUID}', \App\Action\Control\ControlAction::class . ':siteFor');
        $app->put('/site/{siteUID}', \App\Action\Control\ControlAction::class . ':updateSite');
        $app->delete('/site/{siteUID}', \App\Action\Control\ControlAction::class . ':deleteSite');
        $app->post('/site', \App\Action\Control\ControlAction::class . ':createSite');


        $app->get('/controls', \App\Action\Control\ControlAction::class . ':allControls');
        $app->get('/control/{controlUID}', \App\Action\Control\ControlAction::class . ':controlFor');
        $app->put('/control/{controlUID}', \App\Action\Control\ControlAction::class . ':updateControl');
        $app->post('/control', \App\Action\Control\ControlAction::class . ':createControl');
        $app->delete('/control/{controlUID}', \App\Action\Control\ControlAction::class . ':deleteControl');
        $app->post('/control/upload', \App\Action\Control\ControlAction::class . ':upload');


        $app->get('/users', \App\Action\User\UserAction::class . ':allUsers');
        $app->get('/user/{id}', \App\Action\User\UserAction::class . ':getUserById');
        $app->put('/user/{id}', \App\Action\User\UserAction::class . ':updateUser');
        $app->post('/user/', \App\Action\User\UserAction::class . ':createUser');
        $app->delete('/user/{id}', \App\Action\User\UserAction::class . ':deleteUser');
    })->add(\App\Middleware\JwtTokenValidatorMiddleware::class)->add(\App\Middleware\PermissionvalidatorMiddleWare::class);

    };


