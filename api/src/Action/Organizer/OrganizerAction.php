<?php

namespace App\Action\Organizer;

use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentationTransformer;
use App\Domain\Model\Organizer\Service\OrganizerService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrganizerAction
{
    private $organizerService;

    public function __construct(ContainerInterface $c, OrganizerService $organizerService){
        $this->organizerService = $organizerService;
    }

    public function allOrganizers(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write(json_encode($this->organizerService->getAllOrganizers($request->getAttribute('currentuserUid')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createOrganizer(ServerRequestInterface $request, ResponseInterface $response){
        $data = $request->getParsedBody();
        $organizer = new OrganizerRepresentation();
        $organizer->setOrganizationName($data['organization_name'] ?? null);
        $organizer->setDescription($data['description'] ?? null);
        $organizer->setWebsite($data['website'] ?? null);
        $organizer->setWebsitePay($data['website_pay'] ?? null);
        $organizer->setLogoSvg($data['logo_svg'] ?? null);
        $organizer->setContactPersonName($data['contact_person_name'] ?? null);
        $organizer->setEmail($data['email'] ?? null);
        $organizer->setActive($data['active'] ?? true);
        $organizer->setClubUid($data['club_uid'] ?? null);

        $response->getBody()->write(json_encode($this->organizerService->createOrganizer($request->getAttribute('currentuserUid'), $organizer), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getOrganizerById(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $organizerId = (int)$args['organizerId'];
        $response->getBody()->write(json_encode($this->organizerService->getOrganizerById($organizerId, $request->getAttribute('currentuserUid')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateOrganizer(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $organizerId = (int)$args['organizerId'];
        $data = $request->getParsedBody();

        $organizer = new OrganizerRepresentation();
        $organizer->setId($organizerId);
        $organizer->setOrganizationName($data['organization_name'] ?? null);
        $organizer->setDescription($data['description'] ?? null);
        $organizer->setWebsite($data['website'] ?? null);
        $organizer->setWebsitePay($data['website_pay'] ?? null);
        $organizer->setLogoSvg($data['logo_svg'] ?? null);
        $organizer->setContactPersonName($data['contact_person_name'] ?? null);
        $organizer->setEmail($data['email'] ?? null);
        $organizer->setActive($data['active'] ?? true);
        $organizer->setClubUid($data['club_uid'] ?? null);

        $result = $this->organizerService->updateOrganizer($request->getAttribute('currentuserUid'), $organizer);
        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteOrganizer(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $organizerId = (int)$args['organizerId'];
        $response->getBody()->write(json_encode($this->organizerService->deleteOrganizer($request->getAttribute('currentuserUid'), $organizerId), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
} 