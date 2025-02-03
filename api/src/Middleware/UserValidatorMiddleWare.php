<?php

namespace App\Middleware;

use App\common\CurrentOrganizer;
use App\common\CurrentUser;
use App\Domain\Model\Organizer\Repository\OrganizerRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\User;
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


class UserValidatorMiddleWare
{

    private $key;
    private $cs;
    private $path;
    private UserRepository $userRepository;
    private $routeCollector;


    public function __construct(ContainerInterface $c, RouteCollectorInterface $routeCollector, UserRepository $userRepository)
    {
        $this->key = $c->get('settings')['secretkey'];
        $this->cs = $c;
        $this->path = $c->get('settings')['path'];
        $this->userRepository = $userRepository;
        $this->routeCollector = $routeCollector;
    }

    public function __invoke(Request $request, RequestHandler $handler): \Psr\Http\Message\ResponseInterface
    {

        $token = $request->getHeaderLine("TOKEN");

        $signer = new HS256($this->key);
        $parser = new Parser($signer, null);

        try {
            $claims = $parser->parse($token);
        } catch (ValidationException $e) {
            return (new Response())->withStatus(401);
        }

        $user = $this->userRepository->getUserById($claims['id']);

        if (!$user) {
            $roles = $claims['roles'];
            if ($roles['isCompetitor'] === true) {
                $user = new User();
                $user->setId($claims['id']);
                $user->setRoles($claims['roles']);
                CurrentUser::setUser($user);
                return $handler->handle($request);
            }

        }

        if (!$user) {
            return (new Response())->withStatus(401);
        }

        $path = $request->getUri()->getPath();

        if ($this->ignoreApiKey($path)) {
            CurrentUser::setUser(new User());
            return $handler->handle($request);
        }


        CurrentUser::setUser($user);
        $request = $request->withAttribute('userId', $user->getId());
        return $handler->handle($request);


    }

    private function ignoreApiKey(string $url): bool
    {
        $pathToIgnore = $this->pathsToIgnore();
        foreach ($pathToIgnore as $item) {
            if (Strings::contains($url, $item)) {
                return True;
            }
        }
        return False;
    }

    private function pathsToIgnore(): array
    {
        return array("/api/results/year/", "/api/resultList/year", "/api/resultList/test", "/api/track/event/", "/api/tracker/event", "/results/randonneur/", "/track/track", "/tracker/track", "/api/track/", '/api/results');
    }


}