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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Route::middleware('throttle:60,1')->group(function () {

Route::prefix('/api')->group(function () {

    Route::get('/pingapikey', ['middleware' => ['apikey',], function () {
        return 'Testar kontroll av apinyckel';
    }]);

    Route::prefix('/pingdbtest')->group(function () {
        Route::get('/ping', function () {
            dd(env('APP_URL'));
        });
    });

    Route::prefix('/registration')->group(function () {
        Route::get('/ping', function () {
        });
    });

    Route::prefix('/artisan')->group(function () {
        Route::get('/migrate', function () {
            Artisan::call('migrate');
        });
    });
});


//});
