<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'loppservice/api'], function () use ($router) {


    $router->get('/ping', 'PingController@ping');


    $router->group(['prefix' => 'test'], function () use ($router) {


        $router->get('/ping', function () use ($router) {
            return "ping in test";
        });


    });
});
