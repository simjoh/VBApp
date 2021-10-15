<?php

namespace App\Domain\Ping\Service;

use App\Domain\Ping\Repository\PingRepository;

class PingService
{
    /**
     * @var PingRepository
     */
    private PingRepository $repository;

    /**
     * The constructor.
     *
     * @param PingRepository $repository The repository
     */
    public function __construct(PingRepository $repository)
    {
        $this->repository = $repository;
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