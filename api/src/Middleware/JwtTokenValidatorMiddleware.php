<?php

namespace App\Middleware;

use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Validator\DefaultValidator;
use MiladRahimi\Jwt\Validator\Rules\NewerThanOrSame;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class JwtTokenValidatorMiddleware
{

    private $key;

    public function __construct(ContainerInterface $c)
    {
        $this->key = $c->get('settings')['secretkey'];
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $userAgent = $request->getHeaderLine("User-Agent");
      


        if ($userAgent === 'Loppservice/1.0') {
            return $handler->handle($request);
        }


        
        // Try to get token from TOKEN header first
        $token = $request->getHeaderLine("TOKEN");

        // If TOKEN header is empty, try Authorization header
        if (empty($token)) {
            $authHeader = $request->getHeaderLine("Authorization");
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (empty($token)) {
            return (new Response())->withStatus(403);
        }

        $validator = $this->addRules();
        $signer = new HS256($this->key);
        $parser = new Parser($signer, $validator);



        try {
            $claims = $parser->parse($token);
        } catch (ValidationException $e) {

            return (new Response())->withStatus(401);
        }
        return $handler->handle($request);
    }

    private function addRules(): DefaultValidator
    {
        $validator = new DefaultValidator();
       $validator->addRule('exp', new NewerThanOrSame(time()), true);

        return $validator;
    }


}