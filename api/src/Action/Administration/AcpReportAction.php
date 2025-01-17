<?php

namespace App\Action\Administration;

use App\Domain\Model\Acp\Service\AcpService;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Club\Rest\ClubRepresentationTransformer;
use App\Domain\Model\Club\Service\ClubService;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Event\Rest\EventRepresentationTransformer;
use GuzzleHttp\Client;
use Karriere\JsonDecoder\JsonDecoder;
use League\Csv\Writer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class AcpReportAction
{

    private $acpService;

    public function __construct(ContainerInterface $c, AcpService $acpService)
    {
        $this->acpService = $acpService;
    }

    public function getAcpReport(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        $csvContent =  $this->acpService->getAcpReportFor($track_uid, $request->getAttribute('currentuserUid'));

        // Write the content to the response body
        $response->getBody()->write($csvContent);

        // Set headers to indicate a CSV file download
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="users.csv"');

    }

}