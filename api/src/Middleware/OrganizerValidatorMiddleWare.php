<?php

namespace App\Middleware;

use App\common\CurrentOrganizer;
use App\Domain\Model\Organizer\Repository\OrganizerRepository;
use App\Domain\Permission\PermissionRepository;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Parser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Psr7\Response;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;


class OrganizerValidatorMiddleWare
{

    private $key;
    private $cs;
    private $path;
    private OrganizerRepository $organizerRepository;
    private $routeCollector;


    public function __construct(ContainerInterface $c, RouteCollectorInterface $routeCollector, OrganizerRepository $organizerRepository)
    {
        $this->key = $c->get('settings')['secretkey'];
        $this->cs = $c;
        $this->path = $c->get('settings')['path'];
        $this->organizerRepository = $organizerRepository;
        $this->routeCollector = $routeCollector;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        $token = $request->getHeaderLine("TOKEN");

        $signer = new HS256($this->key);
        $parser = new Parser($signer, null);

        try {
            $claims = $parser->parse($token);
        } catch (ValidationException $e) {

            return (new Response())->withStatus(401);
        }

        $organizer = $this->organizerRepository->getById($claims['organizer']);

        if (!$organizer) {
            return (new Response())->withStatus(401);
        }

        CurrentOrganizer::setUser($organizer);

        $request = $request->withAttribute('organizerID', $organizer->getOrganizerId());
        return $handler->handle($request);


    }


}