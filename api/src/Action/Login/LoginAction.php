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
           $jwt = $generator->generate(['id' => $competitor->getId(), 'roles' => $this->getRoles($competitor->getRoles()), 'iat' => time(), 'exp' => time() + 60000000]);
           $competitor->setToken($jwt);
           $ser = new CleanJsonSerializer();
           $response->getBody()->write($ser->serialize($competitor));
           return $response->withStatus(200);
       } else {

           $signer = new HS256($this->key);
           $generator = new Generator($signer);
           // byt till roleid
           $jwt = $generator->generate(['id' => $user->getId(), 'roles' => $this->getRoles($user->getRoles()), 'iat' => time(), 'exp' => time() + 200]);
           $user->setToken($jwt);
           $ser = new CleanJsonSerializer();
           $response->getBody()->write($ser->serialize($user));
           return $response->withStatus(200);
       }

    }

    private function getRoles($roles): array{

        $rolearray = array();
        foreach ($roles as &$value) {
            if($value == 'ADMIN'){
                $rolearray['isAdmin'] = true;
            }
            if($value == 'SUPERUSER'){
                $rolearray['isSuperuser'] = true;
            }
            if($value == 'COMPETITOR'){
                $rolearray['isCompetitor'] = true;
            }
            if($value == 'VOLONTEER'){
                $rolearray['isVolonteer'] = true;
            }
        }
        return $rolearray;
    }





}
