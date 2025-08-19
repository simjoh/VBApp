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
        return  $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0')
            ->withStatus(200);
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

        $requestData = json_decode($request->getBody());

        // Extract track representation and additional form data
        $trackrepresentation = $requestData->trackData ?? $requestData;
        $formData = $requestData->formData ?? null;

        $created = $this->trackService->createTrackFromPlanner($trackrepresentation, $request->getAttribute('currentuserUid'), $formData);

        $response->getBody()->write((string)json_encode($created),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function updateTrackAndEvent(ServerRequestInterface $request, ResponseInterface $response){
        $requestData = json_decode($request->getBody());
        
        // Extract track representation and additional form data
        $trackData = $requestData->trackData ?? $requestData;
        $formData = $requestData->formData ?? null;
        
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $trackUid = $route->getArgument('trackUid');
        
        // Get existing track to preserve event_uid and other required fields
        $existingTrack = $this->trackService->getTrackByUid($trackUid);
        if (!$existingTrack) {
            throw new \Exception("Track not found with UID: " . $trackUid);
        }
        
        // Create TrackRepresentation manually
        $trackrepresentation = new \App\Domain\Model\Track\Rest\TrackRepresentation();
        $trackrepresentation->setTrackUid($trackUid);
        $trackrepresentation->setTitle($trackData->title ?? $existingTrack->getTitle());
        $trackrepresentation->setDescriptions($trackData->description ?? $existingTrack->getDescription());
        $trackrepresentation->setLinktotrack($trackData->link ?? $existingTrack->getLink());
        $trackrepresentation->setDistance((string)($trackData->distance ?? $existingTrack->getDistance()));
        $trackrepresentation->setStartDateTime($trackData->start_date_time ?? $existingTrack->getStartDateTime());
        $trackrepresentation->setActive($trackData->active ?? $existingTrack->isActive());
        $trackrepresentation->setOrganizerId($trackData->organizer_id ?? $existingTrack->getOrganizerId());
        $trackrepresentation->setHeightdifference($existingTrack->getHeightdifference() ?: '0');
        $trackrepresentation->setEventUid($existingTrack->getEventUid());
        
        // Extract checkpoint data if provided
        $checkpointData = $requestData->checkpointData ?? null;
        
        $updatedTrack = $this->trackService->updateTrackAndEvent($trackrepresentation, $request->getAttribute('currentuserUid'), $formData, $checkpointData);
        
        $response->getBody()->write((string)json_encode($updatedTrack),JSON_UNESCAPED_SLASHES);
        return  $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0')
            ->withStatus(200);
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

    public function parseGpxFile(ServerRequestInterface $request, ResponseInterface $response)
    {
        $uploadDir = $this->settings['upload_directory'];
        $uploadedFiles = $request->getUploadedFiles();
        $gpxFile = null;
        foreach ($uploadedFiles as $uploadedFile) {
            $gpxFile = $this->moveUploadedFile($uploadDir, $uploadedFile);
            break;
        }
        if (!$gpxFile) {
            $response->getBody()->write(json_encode(['error' => 'No GPX file uploaded']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $gpxService = new \App\common\Gpx\GpxService();
        $gpxPath = $uploadDir . DIRECTORY_SEPARATOR . $gpxFile;
        $gpx = $gpxService->parseGpxFile($gpxPath);
        if (!$gpxService->validateGpx($gpx)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid GPX file']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $track = $gpxService->gpxToTrackRepresentation($gpx);
        $checkpoints = $gpxService->gpxToCheckpoints($gpx, "", floatval($track->getDistance()));
        // Return as array for frontend
        $result = [
            'track' => [
                'title' => $track->getTitle(),
                'description' => $track->getDescriptions(),
                'link' => $track->getLinktotrack(),
                'distance' => $track->getDistance(),
            ],
            'checkpoints' => array_map(function($cp) {
                return [
                    'lat' => $cp->getSite()->getLat(),
                    'lon' => $cp->getSite()->getLng(),
                    'name' => $cp->getTitle(),
                    'desc' => $cp->getDescription(),
                    'distance' => $cp->getDistance(),
                ];
            }, $checkpoints)
        ];
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * Create a track from GPX data
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function createTrackFromGpx(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            $requestData = json_decode($request->getBody()->getContents(), true);
            $currentuserUid = $request->getAttribute('currentuserUid');
            $formData = $requestData['formData'] ?? null;
            
            $created = $this->trackService->createTrackFromGpxData($requestData, $currentuserUid, $formData);
            
            $response->getBody()->write(json_encode($created));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            
        } catch (\App\common\Exceptions\BrevetException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Internal server error: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
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