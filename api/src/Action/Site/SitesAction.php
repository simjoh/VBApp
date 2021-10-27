<?php

namespace App\Action\Site;

use App\common\Rest\Link;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentationTransformer;
use App\Domain\Model\Site\Service\SiteService;
use App\Domain\Model\User\User;
use InvalidArgumentException;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Transformer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use stdClass;

class SitesAction
{

    private $siteservice;

    public function __construct(ContainerInterface $c, SiteService $siteService)
    {
            $this->siteservice = $siteService;
    }

    public function allSites(ServerRequestInterface $request, ResponseInterface $response){
        $sites = $this->siteservice->allSites();
        $response->getBody()->write(json_encode($sites));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function siteFor(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $site = $this->siteservice->siteFor($route->getArgument('siteUid'));
        $response->getBody()->write((string)json_encode($site));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateSite(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new SiteRepresentationTransformer());
        $siterepresentation = $jsonDecoder->decode($request->getBody(), SiteRepresentation::class);
         $this->siteservice->updateSite($siterepresentation);
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
        $this->siteservice->createSite($siterepresentation);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
