<?php

namespace App\Action\Track;

use App\common\CurrentUser;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentationTransformer;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentation;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentationTransformer;
use App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation;
use App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentationTransformer;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\Track\Rest\TrackRepresentationTransformer;
use App\Domain\Model\Track\Service\TrackService;
use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteContext;
use stdClass;

class TrackAction
{

    private TrackService $trackService;
    private $settings;

    public function __construct(ContainerInterface $c, TrackService $trackService)
    {
        $this->trackService = $trackService;
        $this->settings = $c->get('settings');
    }

    public function allTracks(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write((string)json_encode($this->trackService->allTracks()), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function track(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $response->getBody()->write((string)json_encode($this->trackService->getTrackByTrackUid($route->getArgument('trackUid'), CurrentUser::getUser()->getId())));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function tracksForEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');

        $response->getBody()->write((string)json_encode($this->trackService->tracksForEvent(CurrentUser::getUser()->getId(), $event_uid)), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function publishresults(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $route->getArgument('trackUid');
        $params = $request->getQueryParams();
        $action = filter_var($params["publish"], FILTER_VALIDATE_BOOLEAN);


        $this->trackService->publishResults($route->getArgument('trackUid'), $action);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function updateTrack(ServerRequestInterface $request, ResponseInterface $response)
    {

        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object)$jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $updatedTrack = $this->trackService->updateTrack($trackrepresentation,CurrentUser::getUser()->getId());
        $response->getBody()->write((string)json_encode($updatedTrack), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object)$jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $created = $this->trackService->createTrack($trackrepresentation, CurrentUser::getUser()->getId());
        $response->getBody()->write((string)json_encode($created), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function createTrackFromPlanner(ServerRequestInterface $request, ResponseInterface $response)
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new RusaPlannerResponseRepresentationTransformer());
//        $jsonDecoder->register(new SiteRepresentationTransformer());

        $trackrepresentation = json_decode($request->getBody());

        //  $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), RusaPlannerResponseRepresentation::class);


        $created = $this->trackService->createTrackFromPlanner($trackrepresentation, CurrentUser::getUser()->getId());

        $response->getBody()->write((string)json_encode($created), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function trackplanner(ServerRequestInterface $request, ResponseInterface $response)
    {

        $currentuserUid = $request->getBody()->getContents();

        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new RusaPlannerInputRepresentationTransformer());
        $rusaPlannnerInput = (object)$jsonDecoder->decode($currentuserUid, RusaPlannerInputRepresentation::class);


        $result = $this->trackService->planTrack($rusaPlannnerInput, $currentuserUid);

        $response->getBody()->write(json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function deleteTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->trackService->deleteTrack($route->getArgument('trackUid'), CurrentUser::getUser()->getId());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function buildfromCsv(ServerRequestInterface $request, ResponseInterface $response)
    {
        $uploadDir = $this->settings['upload_directory'];
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles as $uploadedFile) {
            //   if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($uploadDir, $uploadedFile);
            //    }
        }

//        throw new Exception();
        $this->trackService->buildFromCsv($filename, $uploadDir, CurrentUser::getUser()->getId());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
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