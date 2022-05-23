<?php

namespace App\Action\Login;


use App\common\Action\BaseAction;
use App\common\CleanJsonSerializer;
use App\common\Util;
use App\Domain\Authenticate\Service\AuthenticationService;

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

    public function __construct(ContainerInterface $c, AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        $this->key = $c->get('settings')['secretkey'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $formdata = $request->getParsedBody();
        $username = $formdata['username'] ?? false;
        $password = $formdata['password'] ?? false;

        if(!isset($username) || !isset($password)){

           return (new Response())->withStatus(401);
        }


        $user = $this->authenticationService->authenticate($username, $password);

       if($user == null || !isset($user)){
           $competitor = $this->authenticationService->authenticateCompetitor($username, $password);
           if(!isset($competitor)){
               return (new Response())->withStatus(403);
           }
           $signer = new HS256($this->key);
           $generator = new Generator($signer);
           $jwt = $generator->generate(['id' => $competitor->getId(), 'roles' => $this->getRoles($competitor->getRoles()), 'iat' => time(), 'exp' => time() + 86400000]);
           $competitor->setToken($jwt);
           $ser = new CleanJsonSerializer();

           $response->getBody()->write($ser->serialize($competitor));
           return $response->withStatus(200)->withHeader('Content-type','application/json');
       } else {

           $signer = new HS256($this->key);
           $generator = new Generator($signer);

           // byt till roleid
           $jwt = $generator->generate(['id' => $user->getId(), 'roles' => $this->getRoles($user->getRoles()), 'iat' => time(), 'exp' => time() + 86400000]);
           $user->setToken($jwt);
           $ser = new CleanJsonSerializer();
           $response->getBody()->write($ser->serialize($user));
           return $response->withStatus(200)->withHeader('Content-type','application/json;charset=utf-8');
       }

    }

    private function getRoles($roles): array{

        $rolearray = array();

        foreach ($roles as &$value) {
            if($value == 'ADMIN'){
                $rolearray['isAdmin'] = true;
            } else {
                $rolearray['isAdmin'] = false;
            }
            if($value == 'SUPERUSER'){
                $rolearray['isSuperuser'] = true;
            } else {
                $rolearray['isSuperuser'] = false;
            }
            if($value == 'COMPETITOR'){
                $rolearray['isCompetitor'] = true;
            } else {
                $rolearray['isCompetitor'] = false;
            }
            if($value == 'VOLONTEER'){
                $rolearray['isVolonteer'] = true;
            } else {
                $rolearray['isVolonteer'] = false;
            }
            if($value == 'USER'){
                $rolearray['isUser'] = true;
            } else {
                $rolearray['isUser'] = false;
            }
            if($value == 'DEVELOPER'){
                $rolearray['isDeveloper'] = true;
            } else {
                $rolearray['isDeveloper'] = false;
            }
        }
        return $rolearray;
    }





}
