<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ToolController extends Controller
{

    public function index(Request $request)
    {
        return view('tool.show')->with(['migratelink' => '', 'callping' => env("APP_URL") . '/api/ping']);
    }

    public function run(Request $request)
    {
        Artisan::call('migrate', ["--force" => true]);
        Artisan::call('app:country-update');
        return view('tool.show')->with(['migratelink' => '', 'callping' => env("APP_URL") . '/api/ping']);
    }

    public function testappintegration(Request $request)
    {
        $response = Http::withHeaders([
            'APIKEY' => env('BREVET_APP_API_KEY'),
        ])->get(env("BREVET_APP_URL") . '/ping');
        return $response;
    }
}