<?php

namespace App\Action\Login;


use App\common\Action\BaseAction;
use App\common\Util;
use App\Domain\Authenticate\Service\AuthenticationService;
use App\Domain\Model\User\User;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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

        $request->getParsedBody()['username'];
        $request->getParsedBody()['password'];

        $user = $this->authenticationService->authenticate();
        if($user == null){
            return (new Response())->withStatus(403);
        }

        // call loginService
        // Use HS256 to generate and parse tokens
        $signer = new HS256($this->key);

        // Generate a token
        $generator = new Generator($signer);
        $jwt = $generator->generate(['id' => 666, 'is-admin' => true, 'iat' > time(), 'exp' => time() + 60]);
        $user->setToken($jwt);
        // skicka med cookie eller i userobject
        $response->getBody()->write($this->json_encode_private($user));
        return $response->withStatus(200);
    }

}