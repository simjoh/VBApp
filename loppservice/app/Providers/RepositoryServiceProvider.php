<?php

namespace App\Providers;

use App\Repositories\CountyRepository;
use App\Repositories\ErrorEventRepository;
use App\Repositories\EventRepository;
use App\Repositories\Interfaces\CountyRepositoryInterface;
use App\Repositories\Interfaces\ErrorEventRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Repositories\Interfaces\MunicipalityRepositoryInterface;
use App\Repositories\Interfaces\OrganizerRepositoryInterface;
use App\Repositories\Interfaces\PublishedEventRepositoryInterface;
use App\Repositories\MunicipalityRepository;
use App\Repositories\OrganizerRepository;
use App\Repositories\PublishedEventRepository;
use App\Repositories\Interfaces\ClubRepositoryInterface;
use App\Repositories\ClubRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CountyRepositoryInterface::class, CountyRepository::class);
        $this->app->bind(MunicipalityRepositoryInterface::class, MunicipalityRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(OrganizerRepositoryInterface::class, OrganizerRepository::class);
        $this->app->bind(ClubRepositoryInterface::class, ClubRepository::class);
        $this->app->bind(PublishedEventRepositoryInterface::class, PublishedEventRepository::class);
        $this->app->bind(ErrorEventRepositoryInterface::class, ErrorEventRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
