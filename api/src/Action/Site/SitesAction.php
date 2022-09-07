<?php

namespace App\Action\Site;

use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentationTransformer;
use App\Domain\Model\Site\Service\SiteService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteContext;

class SitesAction
{

    private $siteservice;

    public function __construct(ContainerInterface $c, SiteService $siteService)
    {
            $this->siteservice = $siteService;
        $this->settings = $c->get('settings');
    }

    public function allSites(ServerRequestInterface $request, ResponseInterface $response){
        $sites = $this->siteservice->allSites($request->getAttribute('currentuserUid'));
        if(empty($sites)){
            return  $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($sites));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function siteFor(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $site = $this->siteservice->siteFor($route->getArgument('siteUid'),$request->getAttribute('currentuserUid'));
        if(!isset($site)){
            return  $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write((string)json_encode($site));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateSite(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new SiteRepresentationTransformer());
        $siterepresentation = $jsonDecoder->decode($request->getBody(), SiteRepresentation::class);
        $siteUpdated = $this->siteservice->updateSite($siterepresentation, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($siteUpdated));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteSite(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->siteservice->deleteSite($route->getArgument('siteUid'));

        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createSite(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new SiteRepresentationTransformer());

        $siterepresentation  =  $jsonDecoder->decode($request->getBody(), SiteRepresentation::class);

        $response->getBody()->write(json_encode($this->siteservice->createSite($siterepresentation)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function uploadSiteImage(ServerRequestInterface $request, ResponseInterface $response){

        $uploadDir = $this->settings['upload_directory'];
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles as $uploadedFile) {
//            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($uploadDir, $uploadedFile);
                $response->getBody()->write(json_encode($filename));
//            }
        }


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
