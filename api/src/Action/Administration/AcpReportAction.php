<?php

namespace App\Action\Administration;

use App\common\Exceptions\BrevetException;
use App\Domain\Model\Acp\Service\AcpService;
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

        $csvContent = $this->acpService->getAcpReportFor($track_uid, $request->getAttribute('currentuserUid'));

        // Write the content to the response body
        $response->getBody()->write($csvContent);

        // Set headers to indicate a CSV file download
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="users.csv"');

    }

    public function tracksPossibleToReportOn(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        $csvContent = $this->acpService->tracksPossibleToReportOn($track_uid, $request->getAttribute('currentuserUid'));

        // Write the content to the response body
        $response->getBody()->write($csvContent);

        // Set headers to indicate a CSV file download
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }

    public function getFoundationForAcpReport(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        $csvContent = $this->acpService->tracksPossibleToReportOn($track_uid);

        // Write the content to the response body
        $response->getBody()->write($csvContent);

        // Set headers to indicate a CSV file download
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }


    public function createAcpReport(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        throw new BrevetException("not implemented yet", 7, null);


        // Set headers to indicate a CSV file download
        //   return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }


}