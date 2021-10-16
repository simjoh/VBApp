<?php

namespace App\Action\Login;

use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction
{

    private $key;

    public function __construct(ContainerInterface $c)
    {
        $this->key = $c->get('settings')['secretkey'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        // call loginService
        // Use HS256 to generate and parse tokens
        $signer = new HS256($this->key);

        // Generate a token
        $generator = new Generator($signer);
        $jwt = $generator->generate(['id' => 666, 'is-admin' => true, 'iat' > time(), 'exp' => time() + 60]);


        $response->getBody()->write((string)json_encode( "Bearer:" + $jwt));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}