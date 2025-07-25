<?php

namespace App\Middleware;


use Nette\Utils\Strings;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class ApiKeyValidatorMiddleware
{
    private const MAIN_API_KEY = "notsecret_developer_key";
    private const LOPPSERVICE_API_KEY = "testkey";

    public function validate(Request $request, RequestHandler $handler): Response
    {
        $path = $request->getUri()->getPath();

        if($this->ignoreApiKey($path)){
            return $handler->handle($request);
        }

        $api_key_header = $request->getHeaderLine("APIKEY");
        
        // Check if it's a loppservice endpoint
        if ($this->isLoppserviceEndpoint($path)) {
            if ($api_key_header !== self::LOPPSERVICE_API_KEY) {
                $response = (new Response())->withStatus(403);
                return $response;
            }
        } else {
            // Main API endpoints
            if ($api_key_header !== self::MAIN_API_KEY) {
                $response = (new Response())->withStatus(403);
                return $response;
            }
        }

        return $handler->handle($request);
    }

    private function ignoreApiKey(string $url): bool{
        $pathToIgnore = $this->pathsToIgnore();
        foreach ($pathToIgnore as $item){
            if(Strings::contains($url, $item)){
                return True;
            }
        }
        return False;
    }

    private function pathsToIgnore(): array{
        return array("/api/results/year/", "/api/resultList/year", "/api/resultList/test", "/api/track/event/", "/api/tracker/event", "/results/randonneur/", "/track/track","/tracker/track", "/api/track/", '/api/results', "/api/dns/participant");
    }

    private function isLoppserviceEndpoint(string $path): bool {
        return Strings::contains($path, '/loppservice/');
    }
}