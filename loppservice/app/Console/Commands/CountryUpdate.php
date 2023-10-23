<?php

namespace App\Console\Commands;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:country-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for updating countrys from restcountrys every day';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $resp = Http::get('https://restcountries.com/v3.1/all');
        if ($resp->status() == 200) {
            $response = $resp->json();

            foreach ($response as $key => $value) {
                    if (!Country::where('country_code', $value['altSpellings'][0])->exists()) {
                        $country = new Country();
                        $country->country_name_en = $value['name']['common'];
                        $country->country_name_sv = $value['translations']['swe']['common'];
                        $country->country_code = $value['altSpellings'][0];
                        $country->flag_url_svg = $value['flags']['svg'];
                        $country->flag_url_png = $value['flags']['png'];
                        $country->save();
                    } else {
                        Country::where('country_code', $value['altSpellings'][0])
                            ->update([
                                'country_name_en' => $value['name']['common'],
                                'country_name_sv' => $value['translations']['swe']['common'],
                                'country_code' => $value['altSpellings'][0],
                                'flag_url_svg' => $value['flags']['svg'],
                                'flag_url_png' => $value['flags']['png']
                            ]);
                    }
            }
        }
    }
}
