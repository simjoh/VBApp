<?php

namespace App\Http\Controllers;

use App\Interfaces\PingInterface;

class PingController extends Controller
{

    private $pinginterface;


    public function __construct(PingInterface $pinginterface)
    {
        $this->pinginterface = $pinginterface;
    }

    /**
     * Retrieve system status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        $pingval = $this->pinginterface->ping();
        return response()->json(['status' => $pingval]);
    }
}
