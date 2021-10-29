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

    //lägg till ingång för att kunna generera resultat på vb.se

    // Ingång för att kunna visa en cyklists passeringar under ett lopp


    // User route group
    $app->group('/api', function(RouteCollectorProxy $apps) use ($app) {

        // ingångar som används av en cyklist
        $app->get('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}', \App\Action\Randonneur\RandonneurAction::class . ':getCheckpoint');
        $app->post('/randonneur/{uid}/track/{track_uid}/checkpoint/{checkpointUid}/stamp', \App\Action\Randonneur\RandonneurAction::class . ':stamp');
        $app->post('/randonneur/{uid}/track/{track_uid}/checkpoint/{checkpointUid}/markasdnf', \App\Action\Randonneur\RandonneurAction::class . ':markasDNF');
        $app->put('/randonneur/{uid}/track/{track_uid}/checkpoint/{checkpointUid}/rollback', \App\Action\Randonneur\RandonneurAction::class . ':rollbackStamp');

        // ingångar som används av en volontär
        $app->get('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}', \App\Action\Volonteer\VolonteerAction::class . ':getCheckpoint');
        $app->get('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneurs', \App\Action\Volonteer\VolonteerAction::class . ':getRandonneurs');
        $app->post('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/stamp', \App\Action\Volonteer\VolonteerAction::class . ':stamp');
        $app->post('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/dnf', \App\Action\Volonteer\VolonteerAction::class . ':markasDNF');
        $app->post('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/rollback', \App\Action\Volonteer\VolonteerAction::class . ':rollbackStamp');

        // Ingångar för administratörer
        // skapa banor
        $app->get('/tracks', \App\Action\Track\TrackAction::class . ':allTracks');
        $app->get('/track/{trackUid}', \App\Action\Track\TrackAction::class . ':track');
        $app->put('/track/{trackUid}', \App\Action\Track\TrackAction::class . ':updateTrack');
        $app->post('/track', \App\Action\Track\TrackAction::class . ':createTrack');

        // Sites platser där en kotroll kommer att vara
        $app->get('/sites', \App\Action\Site\SitesAction::class . ':allSites');
        $app->get('/site/{siteUid}', \App\Action\Site\SitesAction::class . ':siteFor');
        $app->put('/site/{siteUid}', \App\Action\Site\SitesAction::class . ':updateSite');
        $app->delete('/site/{siteUid}', \App\Action\Site\SitesAction::class . ':deleteSite');
        $app->post('/site', \App\Action\Site\SitesAction::class . ':createSite');

        // byt namn till checkpoints
        $app->get('/checkpoints', \App\Action\Control\CheckpointAction::class . ':allCheckpoints');
        $app->get('/checkpoint/{checkpointUID}', \App\Action\Control\CheckpointAction::class . ':checkpointFor');
        $app->put('/checkpoint/{checkpointUID}', \App\Action\Control\CheckpointAction::class . ':updateCheckpoint');
        $app->post('/checkpoint', \App\Action\Control\CheckpointAction::class . ':createControl');
        $app->delete('/checkpoint/{checkpointUID}', \App\Action\Control\CheckpointAction::class . ':deleteCheckpoint');
        $app->post('/checkpoint/upload', \App\Action\Control\CheckpointAction::class . ':upload');

        // användare i systemet
        $app->get('/users', \App\Action\User\UserAction::class . ':allUsers');
        $app->get('/user/{id}', \App\Action\User\UserAction::class . ':getUserById');
        $app->put('/user/{id}', \App\Action\User\UserAction::class . ':updateUser');
        $app->post('/user/', \App\Action\User\UserAction::class . ':createUser');
        $app->delete('/user/{id}', \App\Action\User\UserAction::class . ':deleteUser');

        // Ingångar för statistik

        // ingång för dashboard

        // Deltagare

    })->add(\App\Middleware\JwtTokenValidatorMiddleware::class)->add(\App\Middleware\PermissionvalidatorMiddleWare::class);

    };


