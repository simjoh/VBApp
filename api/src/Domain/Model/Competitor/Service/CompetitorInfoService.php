<?php

namespace App\Domain\Model\Competitor\Service;

use App\Domain\Model\Competitor\Repository\CompetitorInfoRepository;
use App\Domain\Model\Competitor\Rest\CompetitorInfoAssembly;
use App\Domain\Model\Competitor\Rest\CompetitorInforepresentation;
use App\Domain\Permission\PermissionRepository;
use App\Domain\Model\Country\Service\CountryService;
use App\common\Exception\BrevetException;
use Psr\Container\ContainerInterface;

class CompetitorInfoService
{

    private $settings;
    private $competitorInfoRepository;
    private $permissionrepoitory;
    private $competitorInfoAssembly;
    private $countryService;

    public function __construct(ContainerInterface       $c,
                                CompetitorInfoRepository $competitorInfoRepository, PermissionRepository $permissionRepository, CompetitorInfoAssembly $competitorInfoAssembly, CountryService $countryService)
    {

        $this->settings = $c->get('settings');
        $this->competitorInfoRepository = $competitorInfoRepository;
        $this->permissionrepoitory = $permissionRepository;
        $this->competitorInfoAssembly = $competitorInfoAssembly;
        $this->countryService = $countryService;
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

    /**
     * Update competitor info by competitor UID using CompetitorInfo object
     *
     * @param string $competitor_uid
     * @param \App\Domain\Model\Competitor\CompetitorInfo $competitorInfo
     * @param string $currentuser_id
     * @return CompetitorInforepresentation|null
     */
    public function updateCompetitorInfoByCompetitorUid(string $competitor_uid, \App\Domain\Model\Competitor\CompetitorInfo $competitorInfo, string $currentuser_id): ?CompetitorInforepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);


        // Check if competitor info exists
        $existingCompetitorInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor_uid);
        if (!$existingCompetitorInfo) {
            throw new BrevetException("Competitor info not found for competitor UID: {$competitor_uid}", 404, null);
        }


        // Check if country exists in the database
        $country = $this->countryService->getCountryById($competitorInfo->getCountryId());
        if (!$country) {
            throw new BrevetException("Country with ID {$competitorInfo->getCountryId()} does not exist", 5, null);
        }

        
        $updatedCompetitorInfo = $this->competitorInfoRepository->updateCompetitorInfoByCompetitorUid($competitor_uid, $competitorInfo);
        
        if ($updatedCompetitorInfo) {
            return $this->competitorInfoAssembly->toRepresentation($updatedCompetitorInfo, $permissions);
        }
        
        return null;
    }

    /**
     * Update competitor info by competitor UID using individual parameters
     *
     * @param string $competitor_uid
     * @param string $email
     * @param string $phone
     * @param string $adress
     * @param string $postal_code
     * @param string $place
     * @param string $country
     * @param string $currentuser_id
     * @return CompetitorInforepresentation|null
     */
    public function updateCompetitorInfoByParams(string $competitor_uid, string $email, string $phone, string $adress, string $postal_code, string $place, string $country, string $currentuser_id): ?CompetitorInforepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        
        $updateResult = $this->competitorInfoRepository->updateCompetitorInfoByParams($competitor_uid, $email, $phone, $adress, $postal_code, $place, $country);
        
        if ($updateResult) {
            // Fetch the updated competitor info to return as representation
            $updatedCompetitorInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor_uid);
            if ($updatedCompetitorInfo) {
                return $this->competitorInfoAssembly->toRepresentation($updatedCompetitorInfo, $permissions);
            }
        }
        
        return null;
    }

    /**
     * Create competitor info using the service layer
     *
     * @param \App\Domain\Model\Competitor\CompetitorInfo $competitorInfo
     * @param string $currentuser_id
     * @return CompetitorInforepresentation|null
     */
    public function createCompetitorInfo(\App\Domain\Model\Competitor\CompetitorInfo $competitorInfo, string $currentuser_id): ?CompetitorInforepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        
        $createdCompetitorInfo = $this->competitorInfoRepository->creatCompetitorInfoForCompetitor($competitorInfo);
        
        if ($createdCompetitorInfo) {
            return $this->competitorInfoAssembly->toRepresentation($createdCompetitorInfo, $permissions);
        }
        
        return null;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB", $user_uid);
    }

}