<?php

namespace App\Action\Country;

use App\Domain\Model\Country\Service\CountryService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CountryAction
{
    private $countryService;

    public function __construct(ContainerInterface $c, CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Get all countries
     */
    public function getAllCountries(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $currentUserUid = $request->getAttribute('currentuserUid');
        
        $countries = $this->countryService->getAllCountries($currentUserUid);
        
        $response->getBody()->write(json_encode($countries));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
} 