<?php

namespace App\Middleware;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Response;


class ApiKeyValidatorMiddleware
{


    public function validate(Request $request, RequestHandler $handler): Response
    {

        $path = $request->getUri()->getPath();
        // Ska inte ligga här
        $api_key = "notsecret_developer_key";

        $api_key_header = $request->getHeaderLine("API_KEY");
        if ($api_key_header != $api_key) {
            $response = (new Response())->withStatus(403);

            return $response;
        }

      return $handler->handle($request);

    }

}