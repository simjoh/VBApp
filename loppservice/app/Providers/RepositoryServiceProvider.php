<?php

namespace App\Providers;

use App\Interfaces\PingInterface;
use App\Repositories\PingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PingInterface::class, PingRepository::class);
    }

}
