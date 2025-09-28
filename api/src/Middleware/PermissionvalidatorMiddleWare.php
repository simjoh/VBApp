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

        $userAgent = $request->getHeaderLine("User-Agent");
      


        if ($userAgent === 'Loppservice/1.0') {
            return $handler->handle($request);
        }


        $token = $request->getHeaderLine("TOKEN");

        $signer = new HS256($this->key);
        $parser = new Parser($signer, null);

        try {
            $claims = $parser->parse($token);
        } catch (ValidationException $e) {

            return (new Response())->withStatus(401);
        }


        $permissions = $this->permissionrepository->getPermissionsFor($claims['id']);

        // Check if user has any valid roles
        $roles = $claims['roles'];
        $hasValidRole = false;
        if (is_array($roles)) {
            $hasValidRole = in_array('USER', $roles) || in_array('ADMIN', $roles) || in_array('SUPERUSER', $roles) || 
                           in_array('DEVELOPER', $roles) || in_array('COMPETITOR', $roles) || in_array('VOLONTEER', $roles);
        }
        
        if (empty($permissions) && !$hasValidRole) {
            return (new Response())->withStatus(401);
        }


        // Handle new role format (array of role names)
        $roles = $claims['roles'];
        if (is_array($roles)) {
            if (in_array('USER', $roles) || in_array('ADMIN', $roles) || in_array('SUPERUSER', $roles)) {
                $request = $request->withAttribute('currentuserUid', $claims['id']);
                return $handler->handle($request);
            }

            if (in_array('DEVELOPER', $roles)) {
                $request = $request->withAttribute('currentuserUid', $claims['id']);
                return $handler->handle($request);
            }

            if (in_array('COMPETITOR', $roles)) {
                if (Strings::startsWith($request->getRequestTarget(), $this->path . "randonneur/") === True) {
                    $request = $request->withAttribute('currentuserUid', $claims['id']);
                    return $handler->handle($request);
                } else {
                    return (new Response())->withStatus(401);
                }
            }

            if (in_array('VOLONTEER', $roles)) {
                $request = $request->withAttribute('currentuserUid', $claims['id']);
                return $handler->handle($request);
            }
        }

        // Skicka iväg detta för att kunna sätta rätt länkar osv
        return $handler->handle($request);
    }

}

