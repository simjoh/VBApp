<?php

use App\Middleware\ApiKeyValidatorMiddleware;
use App\Middleware\PermissionvalidatorMiddleWare;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {

    // validate api key for all requests
    $app->add(ApiKeyValidatorMiddleware::class . ':validate');

    // add more endpoints here
    $app->post('/login', \App\Action\Login\LoginAction::class)->setName('login');

    $app->get('/ping', \App\Action\Ping\PingAction::class)->setName('ping');


     //$app->get('/bla/bla/bla', \App\Action\HomeAction::class)->setName('home');




    // User route group
    $app->group('/api', function() use ($app) {


//        $app->group('admin', function(App $app) {
//            $app->get('/billing', function ($request, $response, $args) {
//                // Route for /billing
//            });
//            $this->get('/invoice/{id:[0-9]+}', function ($request, $response, $args) {
//                // Route for /invoice/{id:[0-9]+}
//            });
//        });
//
//        $app->group('competitor', function(App $app) {
//            $app->get('/billing', function ($request, $response, $args) {
//                // Route for /billing
//            });
//            $this->get('/invoice/{id:[0-9]+}', function ($request, $response, $args) {
//                // Route for /invoice/{id:[0-9]+}
//            });
//        });


        $app->get('/users', \App\Action\User\UserAction::class . ':allUsers');
        $app->get('/user/{id}', \App\Action\User\UserAction::class . ':getUserById');
        $app->put('/user/{id}', \App\Action\User\UserAction::class . ':updateUser');
        $app->post('/user/{id}', \App\Action\User\UserAction::class . ':newUser');
        $app->delete('/user/{id}', \App\Action\User\UserAction::class . ':deleteUser');
    })->add(\App\Middleware\JwtTokenValidatorMiddleware::class)->add(\App\Middleware\PermissionvalidatorMiddleWare::class);



    };


