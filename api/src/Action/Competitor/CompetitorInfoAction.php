<?php

namespace App\Action\Competitor;

use App\Domain\Model\Competitor\Rest\CompetitorInforepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorInfoRepresentationTransformer;
use App\Domain\Model\Competitor\Service\CompetitorInfoService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class CompetitorInfoAction
{
    private $competitorInfoService;

    public function __construct(ContainerInterface $c, CompetitorInfoService $competitorInfoService)
    {
        $this->competitorInfoService = $competitorInfoService;
    }

    /**
     * Get competitor info by competitor UID
     */
    public function getCompetitorInfo(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $competitor_uid = $route->getArgument('competitorUid');
        $currentUserUid = $request->getAttribute('currentuserUid');

        $competitorInfo = $this->competitorInfoService->getCompetitorInfoByCompetitorUid($competitor_uid, $currentUserUid);
        
        if (!$competitorInfo) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($competitorInfo));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * Update competitor info by competitor UID
     */
    public function updateCompetitorInfo(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $competitor_uid = $route->getArgument('competitorUid');
        $currentUserUid = $request->getAttribute('currentuserUid');

        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new CompetitorInfoRepresentationTransformer());
        $competitorInfoRepresentation = $jsonDecoder->decode($request->getBody(), CompetitorInforepresentation::class);

        // Convert representation to domain object
        $competitorInfo = new \App\Domain\Model\Competitor\CompetitorInfo();
        $competitorInfo->setCompetitorUid($competitor_uid);
        $competitorInfo->setEmail($competitorInfoRepresentation->getEmail() ?? '');
        $competitorInfo->setPhone($competitorInfoRepresentation->getPhone() ?? '');
        $competitorInfo->setAdress($competitorInfoRepresentation->getAdress() ?? '');
        $competitorInfo->setPostalCode($competitorInfoRepresentation->getPostalCode() ?? '');
        $competitorInfo->setPlace($competitorInfoRepresentation->getPlace() ?? '');
        $competitorInfo->setCountry($competitorInfoRepresentation->getCountry() ?? '');
        if ($competitorInfoRepresentation->getCountryId()) {
            $competitorInfo->setCountryId($competitorInfoRepresentation->getCountryId());
        }

   

        $updatedCompetitorInfo = $this->competitorInfoService->updateCompetitorInfoByCompetitorUid($competitor_uid, $competitorInfo, "");

  

        if (!$updatedCompetitorInfo) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($updatedCompetitorInfo));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * Create competitor info
     */
    public function createCompetitorInfo(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $competitor_uid = $route->getArgument('competitorUid');
        $currentUserUid = $request->getAttribute('currentuserUid');

        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new CompetitorInfoRepresentationTransformer());
        $competitorInfoRepresentation = $jsonDecoder->decode($request->getBody(), CompetitorInforepresentation::class);

        // Convert representation to domain object
        $competitorInfo = new \App\Domain\Model\Competitor\CompetitorInfo();
        $competitorInfo->setCompetitorUid($competitor_uid);
        $competitorInfo->setEmail($competitorInfoRepresentation->getEmail() ?? '');
        $competitorInfo->setPhone($competitorInfoRepresentation->getPhone() ?? '');
        $competitorInfo->setAdress($competitorInfoRepresentation->getAdress() ?? '');
        $competitorInfo->setPostalCode($competitorInfoRepresentation->getPostalCode() ?? '');
        $competitorInfo->setPlace($competitorInfoRepresentation->getPlace() ?? '');
        $competitorInfo->setCountry($competitorInfoRepresentation->getCountry() ?? '');
        if ($competitorInfoRepresentation->getCountryId()) {
            $competitorInfo->setCountryId($competitorInfoRepresentation->getCountryId());
        }

        $createdCompetitorInfo = $this->competitorInfoService->createCompetitorInfo($competitorInfo, $currentUserUid);

        if (!$createdCompetitorInfo) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($createdCompetitorInfo));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /**
     * Update competitor info with individual parameters (query string approach)
     */
    public function updateCompetitorInfoParams(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $competitor_uid = $route->getArgument('competitorUid');
        $currentUserUid = $request->getAttribute('currentuserUid');
        
        $params = $request->getQueryParams();
        
        $email = $params['email'] ?? '';
        $phone = $params['phone'] ?? '';
        $adress = $params['adress'] ?? '';
        $postal_code = $params['postal_code'] ?? '';
        $place = $params['place'] ?? '';
        $country = $params['country'] ?? '';

        $updatedCompetitorInfo = $this->competitorInfoService->updateCompetitorInfoByParams(
            $competitor_uid, $email, $phone, $adress, $postal_code, $place, $country, $currentUserUid
        );

        if (!$updatedCompetitorInfo) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($updatedCompetitorInfo));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
} 