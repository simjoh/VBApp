<?php

namespace App\Action\Login;


use App\common\Action\BaseAction;
use App\common\CleanJsonSerializer;
use App\common\Util;
use App\Domain\Authenticate\Service\AuthenticationService;

use App\Domain\Model\Organizer\Repository\OrganizerRepository;
use App\Domain\Model\Organizer\Service\OrganizerService;
use Exception;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Generator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class LoginAction extends BaseAction
{

    private $key;
    private $authenticationService;
    private OrganizerRepository $organizerRepository;

    public function __construct(ContainerInterface $c, AuthenticationService $authenticationService, OrganizerRepository $organizerRepository)
    {
        $this->authenticationService = $authenticationService;
        $this->organizerRepository = $organizerRepository;
        $this->key = $c->get('settings')['secretkey'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $formdata = $request->getParsedBody();
        $username = $formdata['username'] ?? false;
        $password = $formdata['password'] ?? false;

        if (!isset($username) || !isset($password)) {

            return (new Response())->withStatus(401);
        }


        $user = $this->authenticationService->authenticate($username, $password);


        if ($user == null || !isset($user)) {
            $competitor = $this->authenticationService->authenticateCompetitor($username, $password);
            if (!isset($competitor)) {
                return (new Response())->withStatus(403);
            }
            $signer = new HS256($this->key);
            $generator = new Generator($signer);
            $jwt = $generator->generate(['id' => $competitor->getId(), 'roles' => $this->getRoles($competitor->getRoles()), 'iat' => time(), 'exp' => time() + 345600]);
            $competitor->setToken($jwt);
            $ser = new CleanJsonSerializer();

            $response->getBody()->write($ser->serialize($competitor));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } else {


            $organizer = $this->organizerRepository->getById($user->getOrganizerId());


            $signer = new HS256($this->key);
            $generator = new Generator($signer);


            // byt till roleid
            $jwt = $generator->generate(['id' => $user->getId(), 'roles' => $this->getRoles($user->getRoles()), 'iat' => time(), 'exp' => time() + 86400, 'organizer' => $organizer->getOrganizerId()]);

            $user->setToken($jwt);
            $user->setOrganizerId($organizer->getOrganizerId());
            $ser = new CleanJsonSerializer();
            $response->getBody()->write(json_encode($user));
            // return $response->withStatus(200)->withHeader('Content-type','application/json;charset=utf-8');
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

    }

    private function getRoles($roles): array
    {

        $rolearray = array();


        foreach ($roles as &$value) {
            if ($value == 'ADMIN') {
                $rolearray['isAdmin'] = true;
            } else {
                $rolearray['isAdmin'] = false;
            }
            if ($value == 'SUPERUSER') {
                $rolearray['isSuperuser'] = true;
            } else {
                $rolearray['isSuperuser'] = false;
            }
            if ($value == 'COMPETITOR') {
                $rolearray['isCompetitor'] = true;
            } else {
                $rolearray['isCompetitor'] = false;
            }
            if ($value == 'VOLONTEER') {
                $rolearray['isVolonteer'] = true;
            } else {
                $rolearray['isVolonteer'] = false;
            }
            if ($value == 'USER') {
                $rolearray['isUser'] = true;
            } else {
                $rolearray['isUser'] = false;
            }
            if ($value == 'DEVELOPER') {
                $rolearray['isDeveloper'] = true;
            } else {
                $rolearray['isDeveloper'] = false;
            }
            if ($value == 'ACPREPRESENTIVE') {

                $rolearray['isAcprepresentive'] = true;

            } else {
                $rolearray['isAcprepresentive'] = false;
            }
        }
        return $rolearray;
    }


}
