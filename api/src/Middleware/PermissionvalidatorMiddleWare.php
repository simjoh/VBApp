<?php

namespace App\Middleware;

use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Parser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class PermissionvalidatorMiddleWare
{

    private $key;

    public function __construct(ContainerInterface $c)
    {
        $this->key = $c->get('settings')['secretkey'];
    }


    public function __invoke(Request $request, RequestHandler $handler): Response {

        $token = $request->getHeaderLine("TOKEN");

        $signer = new HS256($this->key);
        $parser = new Parser($signer, null);

        try {
            $claims = $parser->parse($token);
        } catch (ValidationException $e) {
            return (new Response())->withStatus(401);
        }

        // embryo till behörighet till operationer på data. Kolla mot permissions i db och mot uri:s
        if($request->getMethod() == 'GET'){
          //  return (new Response())->withStatus(405);
        }
//        if($request->getMethod() == 'PUT'){
//            print_r($request->getUri());
//        }
//
//        if($request->getMethod() == 'POST'){
//            print_r("POST");
//        }
        if($request->getMethod() == 'DELETE' && !isset($claims['isSuperuser'])){
            return (new Response())->withStatus(401);
        }

        return $handler->handle($request);
    }

}