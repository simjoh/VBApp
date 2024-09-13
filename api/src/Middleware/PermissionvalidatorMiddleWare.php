<?php

namespace App\Middleware;

use App\Domain\Permission\PermissionRepository;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Parser;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Psr7\Response;

class PermissionvalidatorMiddleWare
{

    private $key;
    private $cs;
    private $path;
    private $permissionrepository;
    private $routeCollector;


    public function __construct(ContainerInterface $c, RouteCollectorInterface $routeCollector, PermissionRepository $permissionRepository)
    {
        $this->key = $c->get('settings')['secretkey'];
        $this->cs = $c;
        $this->path = $c->get('settings')['path'];
        $this->permissionrepository = $permissionRepository;
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


        $permissions = $this->permissionrepository->getPermissionsFor($claims['id']);

        if (empty($permissions) && !Arrays::get($claims['roles'], 'isCompetitor') && !Arrays::get($claims['roles'], 'isVolonteer')) {

            return (new Response())->withStatus(401);
        }


        if ((Arrays::get($claims['roles'], 'isUser')) || (Arrays::get($claims['roles'], 'isAdmin')) || (Arrays::get($claims['roles'], 'isSuperuser'))) {

            $request = $request->withAttribute('currentuserUid', $claims['id']);
            return $handler->handle($request);
        };

        if ((Arrays::get($claims['roles'], 'isDeveloper'))) {
            $request = $request->withAttribute('currentuserUid', $claims['id']);
            return $handler->handle($request);
        };

        if ((Arrays::get($claims['roles'], 'isCompetitor'))) {
            if (Strings::startsWith($request->getRequestTarget(), $this->path . "randonneur/") === True) {
                $request = $request->withAttribute('currentuserUid', $claims['id']);
                return $handler->handle($request);
            } else {

                return (new Response())->withStatus(401);
            }
        }

        if ((Arrays::get($claims['roles'], 'isVolonteer'))) {

            $request = $request->withAttribute('currentuserUid', $claims['id']);
            return $handler->handle($request);
//            if(Strings::startsWith($request->getRequestTarget(), "/api/volonteer/") === True){
//                $request = $request->withAttribute('currentuserUid', $claims['id']);
//                return $handler->handle($request);
//            } else {
//                return (new Response())->withStatus(401);
//            }
        }

        // Skicka iväg detta för att kunna sätta rätt länkar osv
        return $handler->handle($request);
    }

}

