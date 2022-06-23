<?php

namespace App\Middleware;

use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Validator\DefaultValidator;
use MiladRahimi\Jwt\Validator\Rules\NewerThan;
use MiladRahimi\Jwt\Validator\Rules\NewerThanOrSame;
use MiladRahimi\Jwt\Validator\Rules\OlderThan;
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
        $token = $request->getHeaderLine("TOKEN");

        if ($token == '') {

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