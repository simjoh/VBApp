<?php





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Route::middleware('throttle:60,1')->group(function () {




Route::prefix('/api')->group(function () {

    Route::get('/ping', function () {

//        $response = Http::get('https://restcountries.com/v3.1/all')->json();
//
//        foreach ($response as $key => $value) {
//            if (!Country::where('country_code', $value['altSpellings'][0])->exists()) {
//                $country = new Country();
//                $country->country_name_en = $value['name']['common'];
//                $country->country_name_sv = $value['translations']['swe']['common'];
//                $country->country_code = $value['altSpellings'][0];
//                $country->flag_url = $value['flags']['svg'];
//                $country->save();
//            } else {
//                Country::where('country_code', $value['altSpellings'][0])
//                    ->update([
//                        'country_name_en' => $value['name']['common'],
//                        'country_name_sv' => $value['translations']['swe']['common'],
//                        'country_code' => $value['altSpellings'][0],
//                        'flag_url' => $value['flags']['svg']
//                    ]);
//            }
//        }

        return 'ping in groupss';
    });


    Route::get('/pingapikey', ['middleware' => ['apikey',], function () {
        return 'Testar kontroll av apinyckel';
    }]);



    Route::prefix('/pingdbtest')->group(function () {

        Route::get('/ping', function () {
        });
    });
});


//});
