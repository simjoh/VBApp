<?php

namespace App\Action\Participant;

use App\common\CurrentUser;
use App\common\Exceptions\BrevetException;
use App\Domain\Model\Loppservice\Rest\LoppserviceParticipantTranformer;
use App\Domain\Model\Loppservice\Rest\LoppservicePersonRepresentation;
use App\Domain\Model\Loppservice\Rest\LoppserviceRegistrationRepresentation;
use App\Domain\Model\Partisipant\Rest\ParticipantInformationRepresentation;
use App\Domain\Model\Partisipant\Rest\ParticipantInformationRepresentationTransformer;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use Exception;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteContext;

class ParticipantAction
{

    private ParticipantService $participantService;
    private $settings;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $c, ParticipantService $participantService)
    {
        $this->participantService = $participantService;
        $this->settings = $c->get('settings');
    }

    public function participants(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participantUid = $route->getArgument('participantUid');
        $part = $this->participantService->participantFor($participantUid, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($part));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getCheckpointsForparticipant(ServerRequestInterface $request, ResponseInterface $response)
    {
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('participantUid');
        $checkpointforrandoneur = $this->participantService->checkpointsForParticipant($participant_uid, CurrentUser::getUser()->getId());
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->setDnf($participant_uid)));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function markasDNS(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->setDns($participant_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    /**
     * @throws BrevetException
     */
    public function stampAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->stampAdmin($participant_uid, $checkpoint_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    /**
     * @throws BrevetException
     */
    public function rollbackstampAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->rollbackstampAdmin($participant_uid, $checkpoint_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackDNF(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->rollbackDnf($participant_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackDNS(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->rollbackDns($participant_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEvent($event_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantsOnTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrack($track_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantsOnTrackMore(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrackWithMoreInformation($track_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $part_uid = $route->getArgument('uid');
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * @throws BrevetException
     */
    public function participantOnEventAndTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEventAndTrack($event_uid, $track_uid, CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateParticipant(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $track_uid = $route->getArgument('trackUid');
        $params = $request->getQueryParams();
        $newTime = $params["newTime"];
        $this->participantService->updateTime($track_uid, $participant_uid, $newTime);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }


    public function updateTime(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $track_uid = $route->getArgument('trackUid');
        $params = $request->getQueryParams();
        $newTime = $params["newTime"];
        $this->participantService->updateTime($track_uid, $participant_uid, $newTime);
//        $this->participantService->updatparticipant($track_uid, $newParticipant);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function addParticipantOntrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $jsonDecoder = new JsonDecoder();

        $json = $request->getBody();
        $data = json_decode($json, true);
        $jsonDecoder->register(new ParticipantInformationRepresentationTransformer());
        $newParticipant = $jsonDecoder->decode($request->getBody()->getContents(), ParticipantInformationRepresentation::class);
        $this->participantService->addParticipantOnTrack($track_uid, $newParticipant);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function addParticipantOntrack2(ServerRequestInterface $request, ResponseInterface $response)
    {


        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new LoppserviceParticipantTranformer());
        $test = json_decode($request->getBody()->getContents(), true);

        $registration = $jsonDecoder->decode($request->getBody()->getContents(), LoppserviceRegistrationRepresentation::class);

        $data = $jsonDecoder->decode($request->getBody()->getContents(), LoppservicePersonRepresentation::class);
        if (isset($data->club)) {
            $club = $data->club;
        }
        try {
            $result = $this->participantService->addParticipantOnTrackFromLoppservice($data, $track_uid, $registration, $club);
            if ($result) {
                $response->getBody()->write(json_encode(['valid' => true, 'test' => 'test', 'response_uid' => $data->response_uid, 'registration_uid' => $registration->registration['registration_uid']]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $result = false;
                $response->getBody()->write(json_encode(['valid' => $result, 'response_uid' => $data->response_uid, 'registration_uid' => $registration->registration['registration_uid'], 'message' => 'Could not create participant']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(501);
            }

        } catch (Exception $e) {
            $result = false;
            $response->getBody()->write(json_encode(['valid' => $result, 'response_uid' => $data->response_uid, 'registration_uid' => $registration->registration['registration_uid'], 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(501);
        }
    }

    /**
     * @throws BrevetException
     */
    public function uploadParticipants(ServerRequestInterface $request, ResponseInterface $response)
    {

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
        $uploadedParticipants = $this->participantService->parseUplodesParticipant($filename, $uploadDir, $track_uid, CurrentUser::getUser()->getId());
        $response->getBody()->write(json_encode($uploadedParticipants));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);


        //return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteParticipant(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->participantService->deleteParticipant($route->getArgument('uid'), CurrentUser::getUser()->getId());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    /**
     * @throws BrevetException
     */
    public function deleteParticipantsontrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->participantService->deleteParticipantsOnTrack($route->getArgument('trackUid'));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    /**
     * @throws BrevetException
     */
    public function addbrevetnumber(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $params = $request->getQueryParams();
        $participant_uid = $route->getArgument('uid');
        $brevenumber = $params["brevenr"];
        $this->participantService->updateParticipantwithbrevenumber($participant_uid, $brevenumber);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
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