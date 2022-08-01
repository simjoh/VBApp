<?php

namespace App\Action\Participant;

use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Partisipant\Rest\EventRepresentationTransformer;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteContext;

class participantAction
{

    public function __construct(ContainerInterface $c, ParticipantService $participantService)
    {
        $this->participantService = $participantService;
        $this->settings = $c->get('settings');
    }

    public function participants(ServerRequestInterface $request, ResponseInterface $response){

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participantUid = $route->getArgument('participantUid');
        $part = $this->participantService->participantFor($participantUid,$request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($part));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getCheckpointsForparticipant(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('participantUid');
        $checkpointforrandoneur =  $this->participantService->checkpointsForParticipant($participant_uid,$request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->setDnf($participant_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function markasDNS(ServerRequestInterface $request, ResponseInterface $response){

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->setDns($participant_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function stampAdmin(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->stampAdmin($participant_uid, $checkpoint_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function rollbackstampAdmin(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->rollbackstampAdmin($participant_uid, $checkpoint_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }




    public function rollbackDNF(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->rollbackDnf($participant_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }




    public function rollbackDNS(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->rollbackDns($participant_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnEvent(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEvent($event_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function participantsOnTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrack($track_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantsOnTrackMore(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrackWithMoreInformation($track_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }



    public function participantOnTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $part_uid = $route->getArgument('uid');
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnEventAndTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEventAndTrack($event_uid ,$track_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateParticipant(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function addParticipantOntrack(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), EventRepresentation::class);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function uploadParticipants(ServerRequestInterface $request, ResponseInterface $response){

        $uploadDir = $this->settings['upload_directory'];
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles as $uploadedFile) {
//            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($uploadDir, $uploadedFile);

//            }
        }
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

       //$track_uid = "c8578cc8-249d-49cc-9a5e-3dd515999534";

        //edfe915c-8928-439c-9ef7-f548645e9918

        $uploadedParticipants = $this->participantService->parseUplodesParticipant($filename, $uploadDir, $track_uid,$request->getAttribute('currentuserUid'));

//        $response->getBody()->write($filename);
        $response->getBody()->write(json_encode($uploadedParticipants));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);


        //return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function deleteParticipant(ServerRequestInterface $request, ResponseInterface $response){
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->participantService->deleteParticipant($route->getArgument('uid'),$currentuserUid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function deleteParticipantsontrack(ServerRequestInterface $request, ResponseInterface $response){

        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->participantService->deleteParticipantsOnTrack($route->getArgument('trackUid'),$currentuserUid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }




    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = $uploadedFile->getClientFilename(); // see http://php.net/manual/en/function.random-bytes.php
        $filename = $basename;
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }


}