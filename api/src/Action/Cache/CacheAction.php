<?php

namespace App\Action\Cache;


use App\common\Action\BaseAction;
use App\common\Service\Cache\CacheService;
use App\Domain\Model\Cache\CacheRepresentation;
use App\Domain\Model\Cache\SvgCacheTransformer;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Club\Rest\ClubRepresentationTransformer;
use App\Domain\Ping\Service\PingService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CacheAction extends BaseAction
{
    private $settings;
    private CacheService $cacheService;

    public function __construct(ContainerInterface $c, CacheService $cacheService)
    {
        $this->settings = $c->get('settings');
        $this->cacheService = $cacheService;
    }

    public function cache(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cacheobject = $this->cacheService->getAllSvgs();
        $retur = array();
        foreach ($cacheobject as $key => $value) {
            $cacherep = new CacheRepresentation();
            $cacherep->setSvgBlob($value->getSvgBlob());
            $cacherep->setOrganizerId($value->getOrganizerId());
            $cacherep->setId($value->getId());
            array_push($retur, $cacherep);
        }

        $response->getBody()->write(json_encode($retur));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    public function create(ServerRequestInterface $request, ResponseInterface $response)
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new SvgCacheTransformer());
        $svgcache = $jsonDecoder->decode($request->getBody()->getContents(), CacheRepresentation::class);
        $response->getBody()->write(json_encode($this->cacheService->saveSvg($svgcache), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);


     //   $response->getBody()->write(json_encode($this->clubservice->createClub(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
      //  return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}