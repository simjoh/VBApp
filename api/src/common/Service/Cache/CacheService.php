<?php

namespace App\common\Service\Cache;

use App\common\CurrentUser;
use App\common\Rest\Link;
use App\Domain\Model\Cache\CacheRepresentation;
use App\Domain\Model\Cache\SvgCache;
use App\Domain\Model\Club\ClubRepository;
use App\Domain\Model\Club\Rest\ClubAssembly;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class CacheService
{

    private $permissionrepoitory;
    private $settings;
    private CacheRepository $cacheRepository;

    public function __construct(ContainerInterface   $c,
                                PermissionRepository $permissionRepository, CacheRepository $cacheRepository)
    {
        $this->settings = $c->get('settings');
        $this->permissionrepoitory = $permissionRepository;
        $this->cacheRepository = $cacheRepository;
    }


    public function getAllSvgs(): ?array
    {
        $svgs = $this->cacheRepository->getAllSvgs();
        $retur = array();

        foreach ($svgs as $key => $value) {
            $svg = (object)$value;
            $svg->setSvgBlob(base64_encode($svg->getSvgBlob()));
            array_push($retur, $svg);
        }
        return $retur;
    }

    public function saveSvg(CacheRepresentation $cacheRepresentation)
    {

        $currentUser = CurrentUser::getUser();

        $svg = new SvgCache();
        $svg->setSvgBlob($cacheRepresentation->getSvgBlob());
        $svg->setOrganizerId(CurrentUser::getUser()->getOrganizerId());
        return $this->cacheRepository->saveSvg(new SvgCache());
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB", $user_uid);
    }

}