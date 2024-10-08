<?php

namespace App\Domain\Model\Competitor\Service;

use App\Domain\Model\Competitor\Repository\CompetitorInfoRepository;
use App\Domain\Model\Competitor\Rest\CompetitorInfoAssembly;
use App\Domain\Model\Competitor\Rest\CompetitorInforepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class CompetitorInfoService
{

    private $settings;
    private $competitorInfoRepository;
    private $permissionrepoitory;
    private $competitorInfoAssembly;

    public function __construct(ContainerInterface       $c,
                                CompetitorInfoRepository $competitorInfoRepository, PermissionRepository $permissionRepository, CompetitorInfoAssembly $competitorInfoAssembly)
    {

        $this->settings = $c->get('settings');
        $this->competitorInfoRepository = $competitorInfoRepository;
        $this->permissionrepoitory = $permissionRepository;
        $this->competitorInfoAssembly = $competitorInfoAssembly;

    }

    public function getCompetitorInfoByCompetitorUid(string $competitor_uid, string $currentuser_id): ?CompetitorInforepresentation
    {

        $permissions = $this->getPermissions($currentuser_id);
        $competitorInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor_uid);
        if ($competitorInfo) {
            return $this->competitorInfoAssembly->toRepresentation($competitorInfo, $permissions);
        } else {
            return new CompetitorInforepresentation();
        }
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB", $user_uid);
    }

}