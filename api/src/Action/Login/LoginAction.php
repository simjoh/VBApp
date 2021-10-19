<?php

namespace App\Action\Login;


use App\common\Action\BaseAction;
use App\common\Util;
use App\Domain\Authenticate\Service\AuthenticationService;

use Exception;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
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
           return (new Response())->withStatus(403);
        }

        $user = $this->authenticationService->authenticate($username, $password);

       if($user == null || !isset($user)){
           $competitor = $this->authenticationService->authenticateCompetitor($username, $password);

           if(!isset($competitor)){
               return (new Response())->withStatus(403);
           }
           $signer = new HS256($this->key);
           $generator = new Generator($signer);
           $jwt = $generator->generate(['id' => $competitor->getId(), 'is-admin' => false, 'iat' > time(), 'exp' => time() + 60]);
           $competitor->setToken($jwt);
           $response->getBody()->write($this->json_encode_private($competitor));
           return $response->withStatus(200);
       } else {

           $signer = new HS256($this->key);
           $generator = new Generator($signer);
           $jwt = $generator->generate(['id' => $user->getId(), ['is-admin' => true, 'is-developer' => false], 'iat' > time(), 'exp' => time() + 60]);
           $user->setToken($jwt);
           $response->getBody()->write($this->json_encode_private($user));
           return $response->withStatus(200);
       }

    }





}
