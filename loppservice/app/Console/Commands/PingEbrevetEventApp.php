<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PingEbrevetEventApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ping-ebrevet-event-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'APIKEY' => env('BREVET_APP_API_KEY'),
            'User-Agent' => env('LOPPSERVICE_USER_AGENT')
        ])->get(env("BREVET_APP_URL") . '/ping');

        Log::info($response->status());
    }
}
