<?php

namespace App\Action\Site;

use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Service\SiteService;
use App\Domain\Model\Site\Site;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

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
        $this->siteservice->siteFor("");
        $response->getBody()->write((string)json_encode(['site med  uid' => true]));
    }


    public function updateSite(ServerRequestInterface $request, ResponseInterface $response){
        $this->siteservice->updateSite("");
        $response->getBody()->write((string)json_encode(['site med  uid' => true]));
    }

    public function deleteSite(ServerRequestInterface $request, ResponseInterface $response){
        $this->siteservice->updateSite("");
        $response->getBody()->write((string)json_encode(['site med  uid' => true]));
    }


    public function createSite(ServerRequestInterface $request, ResponseInterface $response){
        $this->siteservice->createSite();
        $response->getBody()->write((string)json_encode(['site med  uid' => true]));
    }

}