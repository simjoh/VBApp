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
           $jwt = $generator->generate(['id' => $competitor->getId(), 'roles' => $this->getRoles($competitor->getRoles()), 'iat' => time(), 'exp' => time() + 345600]);
           $competitor->setToken($jwt);
           $ser = new CleanJsonSerializer();

           $response->getBody()->write($ser->serialize($competitor));
           return $response->withStatus(200)->withHeader('Content-type','application/json');
       } else {

           $signer = new HS256($this->key);
           $generator = new Generator($signer);

           // byt till roleid
           $jwt = $generator->generate(['id' => $user->getId(), 'roles' => $this->getRoles($user->getRoles()), 'organizer_id' => $user->getOrganizerId(), 'iat' => time(), 'exp' => time() + 86400]);
           $user->setToken($jwt);
           $ser = new CleanJsonSerializer();
           
           $response->getBody()->write($ser->serialize($user));
           return $response->withStatus(200)->withHeader('Content-type','application/json;charset=utf-8');
       }

    }

    private function getRoles($roles): array{

        $rolearray = array();

        foreach ($roles as &$value) {
            if($value == \App\common\Context\UserContext::ROLE_ADMIN){
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_ADMIN] = true;
            } else {
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_ADMIN] = false;
            }
            if($value == \App\common\Context\UserContext::ROLE_SUPERUSER){
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_SUPERUSER] = true;
            } else {
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_SUPERUSER] = false;
            }
            if($value == \App\common\Context\UserContext::ROLE_COMPETITOR){
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_COMPETITOR] = true;
            } else {
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_COMPETITOR] = false;
            }
            if($value == \App\common\Context\UserContext::ROLE_VOLONTEER){
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_VOLONTEER] = true;
            } else {
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_VOLONTEER] = false;
            }
            if($value == \App\common\Context\UserContext::ROLE_USER){
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_USER] = true;
            } else {
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_USER] = false;
            }
            if($value == \App\common\Context\UserContext::ROLE_DEVELOPER){
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_DEVELOPER] = true;
            } else {
                $rolearray[\App\common\Context\UserContext::ROLE_PROP_IS_DEVELOPER] = false;
            }
        }
        return $rolearray;
    }





}
