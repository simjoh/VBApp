<?php

namespace App\Domain\Ping\Service;

use App\common\Rest\LoppserviceRestClient;
use App\Domain\Ping\Repository\PingRepository;

class PingService
{
    /**
     * @var PingRepository
     */
    private PingRepository $repository;
    private LoppserviceRestClient $loppserviceRestClient;

    /**
     * The constructor.
     *
     * @param PingRepository $repository The repository
     */
    public function __construct(PingRepository $repository,LoppserviceRestClient $loppserviceRestClient)
    {



        $this->repository = $repository;
        $this->loppserviceRestClient = $loppserviceRestClient;
    }

    /**
     *
     * @return boolean
     */
    public function ping(): bool
    {
        // Insert user
        return  $this->repository->ping();
    }

}