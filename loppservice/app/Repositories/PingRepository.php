<?php

namespace App\Repositories;

use App\Interfaces\PingInterface;
use Illuminate\Support\Facades\DB;

class PingRepository implements PingInterface
{

    public function ping(): string
    {
        try {
            $conn = DB::connection()->getPdo();
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e);
        }

//        event(new ExampleEvent);
        return "healthy";
        // TODO: Implement ping() method.
    }
}
