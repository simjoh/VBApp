<?php

namespace App\Action\Track;

use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentationTransformer;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentation;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentationTransformer;
use App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation;
use App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentationTransformer;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\Track\Rest\TrackRepresentationTransformer;
use App\Domain\Model\Track\Service\TrackService;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentationTranformer;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
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
    private CheckpointsService $checkpointService;
    private $settings;

    public function __construct(ContainerInterface $c, 
                              TrackService $trackService,
                              CheckpointsService $checkpointService)
    {
        $this->trackService = $trackService;
        $this->checkpointService = $checkpointService;
        $this->settings = $c->get('settings');
    }

    public function allTracks(ServerRequestInterface $request, ResponseInterface $response){
      $currentuserUid = $request->getAttribute('currentuserUid');
        $response->getBody()->write((string)json_encode( $this->trackService->allTracks($currentuserUid)), JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function track(ServerRequestInterface $request, ResponseInterface $response){
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $response->getBody()->write((string)json_encode( $this->trackService->getTrackByTrackUid($route->getArgument('trackUid'),$currentuserUid)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function tracksForEvent(ServerRequestInterface $request, ResponseInterface $response){
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');

        $response->getBody()->write((string)json_encode( $this->trackService->tracksForEvent($currentuserUid, $event_uid)), JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function publishresults(ServerRequestInterface $request, ResponseInterface $response){
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $route->getArgument('trackUid');
        $params = $request->getQueryParams();
        $action = filter_var($params["publish"], FILTER_VALIDATE_BOOLEAN);


        $this->trackService->publishResults($route->getArgument('trackUid'),$action ,$currentuserUid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function updateTrack(ServerRequestInterface $request, ResponseInterface $response){

        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $updatedTrack = $this->trackService->updateTrack($trackrepresentation, $request->getAttribute('currentuserUid'));
        $response->getBody()->write((string)json_encode($updatedTrack),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createTrack(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $created = $this->trackService->createTrack($trackrepresentation,  $request->getAttribute('currentuserUid'));
        $response->getBody()->write((string)json_encode($created),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function createTrackWithOutCheckpoints(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $created = $this->trackService->createTrackWithOutCheckpoints($trackrepresentation,  $request->getAttribute('currentuserUid'));
        $response->getBody()->write((string)json_encode($created),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }



    public function createTrackFromPlanner(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new RusaPlannerResponseRepresentationTransformer());
//        $jsonDecoder->register(new SiteRepresentationTransformer());

        $trackrepresentation = json_decode($request->getBody());

      //  $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), RusaPlannerResponseRepresentation::class);



         $created = $this->trackService->createTrackFromPlanner($trackrepresentation,  $request->getAttribute('currentuserUid'));

        $response->getBody()->write((string)json_encode($created),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function addCheckpointsToTrack(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new CheckpointRepresentationTranformer());
        $checkpoints = (array) json_decode($request->getBody());
        
        $updatedTrack = $this->checkpointService->addCheckpointsToTrack($track_uid, $checkpoints);
        
        $response->getBody()->write((string)json_encode($updatedTrack), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function trackplanner(ServerRequestInterface $request, ResponseInterface $response){
        try {
            $currentuserUid = $request->getBody()->getContents();

            $jsonDecoder = new JsonDecoder();
            $jsonDecoder->register(new RusaPlannerInputRepresentationTransformer());
            
            try {
                $rusaPlannnerInput = (object) $jsonDecoder->decode($currentuserUid, RusaPlannerInputRepresentation::class);
                
                $result = $this->trackService->planTrack($rusaPlannnerInput, $currentuserUid);
                
                $response->getBody()->write(json_encode($result), JSON_UNESCAPED_SLASHES);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } catch (\Exception $e) {
                // Handle JSON decode errors or other exceptions
                error_log("Error processing track plan request: " . $e->getMessage());
                $errorResponse = new \stdClass();
                $errorResponse->error = "Failed to process request: " . $e->getMessage();
                $response->getBody()->write(json_encode($errorResponse));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch (\Throwable $t) {
            // Catch-all for any uncaught errors
            error_log("Uncaught error in trackplanner: " . $t->getMessage());
            $errorResponse = new \stdClass();
            $errorResponse->error = "An unexpected error occurred";
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }


    public function deleteTrack(ServerRequestInterface $request, ResponseInterface $response){
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->trackService->deleteTrack($route->getArgument('trackUid'),$currentuserUid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }





    public function buildfromCsv(ServerRequestInterface $request, ResponseInterface $response){
        $uploadDir = $this->settings['upload_directory'];
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles as $uploadedFile) {
         //   if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($uploadDir, $uploadedFile);
        //    }
        }

//        throw new Exception();
        $this->trackService->buildFromCsv($filename, $uploadDir, $request->getAttribute('currentuserUid'));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
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