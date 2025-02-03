<?php

namespace App\Action\Administration;

use App\common\Exceptions\BrevetException;
use App\common\Rest\AcpReportRestClient;
use App\Domain\Model\Acp\Rest\AcpReportRepresentation;
use App\Domain\Model\Acp\Rest\AcpReportRepresentationTransformer;
use App\Domain\Model\Acp\Service\AcpService;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentationTransformer;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class AcpReportAction
{

    private $acpService;
    private AcpReportRestClient $acpReportRestClient;

    public function __construct(ContainerInterface $c, AcpService $acpService, AcpReportRestClient $acpReportRestClient)
    {
        $this->acpService = $acpService;
        $this->acpReportRestClient = $acpReportRestClient;
    }

    public function getAcpReport(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        $csvContent = $this->acpService->getAcpReportAsCsv($track_uid);

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

        $csvContent = $this->acpService->tracksPossibleToReportOn($track_uid);

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
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new AcpReportRepresentationTransformer());
        $acpreport = $jsonDecoder->decode($request->getBody()->getContents(), AcpReportRepresentation::class);
        $this->acpService->createReport($acpreport);
        $response->getBody()->write(json_encode($this->acpService->createReport($acpreport)));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function markAsReadyForApproval(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $report_uid = $route->getArgument('report_uid');

        $this->acpService->markAsreadyForApproval($report_uid);
        throw new BrevetException("not implemented yet", 7, null);
        // Set headers to indicate a CSV file download
        // return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }


    public function approveReport(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $report_uid = $route->getArgument('report_uid');
        $acpreport = $this->acpService->reportToAcp($report_uid);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }


    public function deletereport(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $report_uid = $route->getArgument('report_uid');
        $result = $this->acpService->deleteReport($report_uid);
        if ($result) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            return false;
        }
    }
}