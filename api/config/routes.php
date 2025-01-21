<?php

use App\Middleware\ApiKeyValidatorMiddleware;
use App\Middleware\CleanupMiddleware;
use App\Middleware\CleanupUserMiddleware;
use App\Middleware\OrganizerValidatorMiddleWare;
use App\Middleware\UserValidatorMiddleWare;
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
    // Hämtar vyn för en resultat på ett event för ett event och år.
    $app->get('/results/year/{year}/event/{eventUid}', \App\Controller\ResultsController::class . ':getResultView')->setName('result');
    //
    $app->get('/results/event/{eventUid}', \App\Controller\ResultsController::class . ':getResultForEvent')->setName('resultonevent');
    $app->get('/results/track/{trackUid}', \App\Controller\ResultsController::class . ':getResultOnTrack')->setName('resultontrack');
    // Hämtar själva resultatlistan för ett event.
    $app->get('/resultList/year/{year}/event/{eventUid}', \App\Controller\ResultsController::class . ':getResultList');
    // resultat för en deltagare en person.
    //$app->put('/results/participant/{participantUid}', \App\Controller\ResultsController::class . ':resultForContestant');

    $app->get('/results/randonneur/{uid}/view', \App\Controller\ResultsController::class . ':getResultViewForContestant')->setName('resultcontestant');
    $app->get('/results/randonneur/{uid}', \App\Controller\ResultsController::class . ':resultForContestant');

    $app->get('/resultList/test', \App\Controller\ResultsController::class . ':getResultsPhp');

    // Ingång för att kunna visa en cyklists passeringar under ett lopp. hämta vy
    $app->get('/track/event/{eventUid}', \App\Controller\ResultsController::class . ':getTrackView')->setName('track');
    // Tracker för ett event dvs själva listan.
    $app->get('/tracker/event/{eventUid}', \App\Controller\ResultsController::class . ':track');

    $app->get('/track/track/{trackUid}', \App\Controller\ResultsController::class . ':getTrackOnTrackView')->setName('trackontrack');
    $app->get('/tracker/track/{trackUid}', \App\Controller\ResultsController::class . ':trackrandonneurontrack');
    $app->get('/track/{trackUid}/participant/{participantUid}/view', \App\Controller\ResultsController::class . ':gettrackranonneurview')->setName('trackranonneur');
    $app->get('/track/{trackUid}/participant/{participantUid}/checkpoints', \App\Controller\ResultsController::class . ':gettrackranonneurcheckpoints');
    $app->post('/participant/addparticipant/track/{trackUid}', \App\Action\Participant\ParticipantAction::class . ':addParticipantOntrack2');
    // User route group
    $app->group('/api', function(RouteCollectorProxy $apps) use ($app) {

        // ingångar som används av en cyklist
        $app->get('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}', \App\Action\Randonneur\RandonneurAction::class . ':getCheckpoint');
        $app->get('/randonneur/preview/checkpoints/track/{track_uid}', \App\Action\Randonneur\RandonneurAction::class . ':getCheckpointPreView');
        $app->get('/randonneur/track/{track_uid}', \App\Action\Randonneur\RandonneurAction::class . ':getTrack');
        $app->post('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/stamp', \App\Action\Randonneur\RandonneurAction::class . ':stamp');
        $app->put('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/markasdnf', \App\Action\Randonneur\RandonneurAction::class . ':markasDNF');
        $app->put('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/rollbackdnf', \App\Action\Randonneur\RandonneurAction::class . ':rollbackDNF');
        $app->put('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/rollback', \App\Action\Randonneur\RandonneurAction::class . ':rollbackStamp');
        $app->put('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/checkoutFrom', \App\Action\Randonneur\RandonneurAction::class . ':checkoutFrom');
        $app->put('/randonneur/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/undocheckoutFrom', \App\Action\Randonneur\RandonneurAction::class . ':undocheckoutFrom');

        // ingångar som används av en volontär
        $app->get('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}', \App\Action\Volonteer\VolonteerAction::class . ':getCheckpoint');
        $app->get('/volonteer/track/{trackUid}/checkpoints', \App\Action\Volonteer\VolonteerAction::class . ':getCheckpoints');
        $app->get('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneurs', \App\Action\Volonteer\VolonteerAction::class . ':getRandonneurs');
        $app->post('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/stamp', \App\Action\Volonteer\VolonteerAction::class . ':stamp');
        $app->post('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/dnf', \App\Action\Volonteer\VolonteerAction::class . ':markasDNF');
        $app->put('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/rollbackdnf', \App\Action\Volonteer\VolonteerAction::class . ':rollbackDNF');
        $app->put('/volonteer/track/{trackUid}/checkpoint/{checkpointUid}/randonneur/{uid}/rollback', \App\Action\Volonteer\VolonteerAction::class . ':rollbackStamp');
        $app->put('/volonteer/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/checkoutfrom', \App\Action\Volonteer\VolonteerAction::class . ':checkoutFrom');
        $app->put('/volonteer/{uid}/track/{track_uid}/startnumber/{startnumber}/checkpoint/{checkpointUid}/undocheckoutfrom', \App\Action\Volonteer\VolonteerAction::class . ':undocheckoutFrom');

        // Ingångar för administratörer
        // skapa banor och läsa banor
        $app->get('/tracks', \App\Action\Track\TrackAction::class . ':allTracks');
        $app->get('/track/{trackUid}', \App\Action\Track\TrackAction::class . ':track');
        $app->put('/track/{trackUid}', \App\Action\Track\TrackAction::class . ':updateTrack');
        $app->post('/track', \App\Action\Track\TrackAction::class . ':createTrack');
        $app->get('/tracks/event/{eventUid}', \App\Action\Track\TrackAction::class . ':tracksForEvent');
        $app->delete('/track/{trackUid}', \App\Action\Track\TrackAction::class . ':deleteTrack');
        $app->put('/publishresults/track/{trackUid}', \App\Action\Track\TrackAction::class . ':publishresults');

        $app->post('/buildlEventAndTrackFromCsv/upload', \App\Action\Track\TrackAction::class  . ':buildfromCsv');

        $app->post('/trackplanner', \App\Action\Track\TrackAction::class  . ':trackplanner');

        $app->post('/trackplanner/createtrackfromplanner', \App\Action\Track\TrackAction::class  . ':createTrackFromPlanner');



        // event
        $app->get('/events', \App\Action\Event\EventAction::class . ':allEvents');
        $app->get('/event/{eventUid}', \App\Action\Event\EventAction::class  . ':eventFor');
        $app->get('/events/eventInformation', \App\Action\Event\EventAction::class  . ':eventInformation');
        $app->put('/event/{eventUid}', \App\Action\Event\EventAction::class  . ':updateEvent');
        $app->post('/event/', \App\Action\Event\EventAction::class . ':createEvent');
        $app->delete('/event/{eventUid}', \App\Action\Event\EventAction::class  . ':deleteEvent');

        // Sites platser där en kotroll kommer att vara
        $app->get('/sites', \App\Action\Site\SitesAction::class . ':allSites');
        $app->get('/site/{siteUid}', \App\Action\Site\SitesAction::class . ':siteFor');
        $app->put('/site/{siteUid}', \App\Action\Site\SitesAction::class . ':updateSite');
        $app->delete('/site/{siteUid}', \App\Action\Site\SitesAction::class . ':deleteSite');
        $app->post('/site', \App\Action\Site\SitesAction::class . ':createSite');
        $app->post('/site/upload', \App\Action\Site\SitesAction::class . ':uploadSiteImage');

        // byt namn till checkpoints
        $app->get('/checkpoints', \App\Action\Checkpoint\CheckpointAction::class . ':allCheckpoints');
        $app->get('/checkpoint/{checkpointUID}', \App\Action\Checkpoint\CheckpointAction::class . ':checkpointFor');
        $app->put('/checkpoint/{checkpointUID}', \App\Action\Checkpoint\CheckpointAction::class . ':updateCheckpoint');
        $app->post('/checkpoint', \App\Action\Checkpoint\CheckpointAction::class . ':createCheckpoint');
        $app->delete('/checkpoint/{checkpointUID}', \App\Action\Checkpoint\CheckpointAction::class . ':deleteCheckpoint');
        $app->post('/checkpoint/upload', \App\Action\Checkpoint\CheckpointAction::class . ':upload');

        // användare i systemet
        $app->get('/users', \App\Action\User\UserAction::class . ':allUsers')->setName("allUsers");
        $app->get('/user/{id}', \App\Action\User\UserAction::class . ':getUserById')->setName("user");
        $app->put('/user/{id}', \App\Action\User\UserAction::class . ':updateUser')->setName("updateUser");;
        $app->post('/user/', \App\Action\User\UserAction::class . ':createUser')->setName('createUser');
        $app->delete('/user/{id}', \App\Action\User\UserAction::class . ':deleteUser')->setName('deleteUser');

        // Ingångar för statistik
        // ingång för dashboard

        // Deltagare på olika banor och event
        $app->get('/participants/event/{eventUid}', \App\Action\Participant\ParticipantAction::class. ':participantOnEvent');

        // admin ska också kunna checka in och behöver kunna läsa upp dessa checkpoints och kunna stämpla in eller sätta dnf. Då nästan utan kontroller
        $app->get('/participant/{participantUid}/checkpointsforparticipant', \App\Action\Participant\ParticipantAction::class. ':getCheckpointsForparticipant');
        $app->put('/participant/{uid}/checkpoint/{checkpointUid}/rollbackstamp', \App\Action\Participant\ParticipantAction::class . ':rollbackstampAdmin');
        $app->put('/participant/{uid}/checkpoint/{checkpointUid}/stamp', \App\Action\Participant\ParticipantAction::class . ':stampAdmin');
        $app->put('/participant/{uid}/setdnf', \App\Action\Participant\ParticipantAction::class . ':markasDNF');
        $app->put('/participant/{uid}/setdns', \App\Action\Participant\ParticipantAction::class . ':markasDNS');
        $app->put('/participant/{uid}/rollbackdnf', \App\Action\Participant\ParticipantAction::class  . ':rollbackDNF');
        $app->put('/participant/{uid}/rollbackdns', \App\Action\Participant\ParticipantAction::class  . ':rollbackDNS');


        $app->get('/participants/event/{eventUid}/track/{trackUid}', \App\Action\Participant\ParticipantAction::class. ':participantOnEventAndTrack');
        $app->get('/participants/{trackUid}', \App\Action\Participant\ParticipantAction::class . ':participantsOnTrack');
        $app->get('/participants/track/{trackUid}/extended', \App\Action\Participant\ParticipantAction::class . ':participantsOnTrackMore');
        $app->get('/participant/{participantUid}', \App\Action\Participant\ParticipantAction::class . ':participants');
        $app->get('/participant/{uid}/track/{trackUid}', \App\Action\Participant\ParticipantAction::class . ':participantOnTrack');
        $app->put('/participant/{uid}/track/{trackUid}/update', \App\Action\Participant\ParticipantAction::class . ':updateParticipant');
        $app->put('/participant/{uid}/track/{trackUid}/updateTime', \App\Action\Participant\ParticipantAction::class . ':updateTime');
        $app->put('/participant/{uid}/track/{trackUid}/addbrevetnumber', \App\Action\Participant\ParticipantAction::class . ':addbrevetnumber');

     //   $app->post('/participants/{trackUid}/upload', \App\Action\Participant\ParticipantAction::class . ':uploadParticipants');
        $app->post('/participants/upload/track/{trackUid}', \App\Action\Participant\ParticipantAction::class . ':uploadParticipants');
        $app->delete('/participant/{uid}/deleteParticipant', \App\Action\Participant\ParticipantAction::class . ':deleteParticipant');

        $app->delete('/participants/deleteParticipants/{trackUid}', \App\Action\Participant\ParticipantAction::class . ':deleteParticipantsontrack');
        //Ingång för att lägga tillbrevenr i efterhand.

        // lägg till ingångar för admin av klubbar
        $app->get('/allclubs', \App\Action\Club\ClubAction::class . ':allClubs');
        // $app->get('/club/club/{clubUid}', \App\Action\Club\ClubAction::class . ':allUsers')->setName("club");
        $app->post('/club/createclub', \App\Action\Club\ClubAction::class . ':createClub');
        // $app->put('/club/updateClub/{clubUid}', \App\Action\Club\ClubAction::class . ':allUsers')->setName("updateClub");
        // $app->delete('/club/deleteClub/{clubUid}', \App\Action\Club\ClubAction::class . ':allUsers')->setName("deleteClub");

        // roller i systemet endast läsa


        $app->get('/administration/acpreport/track/{trackUid}', \App\Action\Administration\AcpReportAction::class. ':getAcpReport');
        $app->get('/administration/acpreport/tracks', \App\Action\Administration\AcpReportAction::class. ':tracksPossibleToReportOn');
        $app->get('/administration/acpreport/foundation/track/{trackUid}', \App\Action\Administration\AcpReportAction::class. ':getFoundationForAcpReport');
        $app->post('/administration/acpreport/report/track/{trackUid}', \App\Action\Administration\AcpReportAction::class. ':createAcpReport');


        // Arrangör
        $app->get('/organizers/organizer/{organizerID}', \App\Action\Organizers\OrganizerAction::class. ':getOrganizer');
        $app->get('/organizers', \App\Action\Organizers\OrganizerAction::class. ':allOrganizers');
        $app->post('/organizers', \App\Action\Organizers\OrganizerAction::class. ':createOrganizer');


    })->add(CleanupMiddleware::class)->add(CleanupUserMiddleware::class)->add(\App\Middleware\JwtTokenValidatorMiddleware::class)->add(\App\Middleware\PermissionvalidatorMiddleWare::class)->add(OrganizerValidatorMiddleWare::class)->add(UserValidatorMiddleWare::class);

    };


