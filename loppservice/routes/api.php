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


use App\Http\Controllers\EventController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\EventGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware('throttle:60,1')->group(function () {

Route::prefix('/api')->group(function () {


    Route::get('/ping' , [ToolController::class, 'testappintegration']);

    Route::post('/transfer' , [ToolController::class, 'publishToCyclingappIfNotAlreadyRegister']);

    Route::get('/pingapikey', ['middleware' => ['apikey',], function () {
        return 'Testar kontroll av apinyckel';
    }]);

    Route::prefix('/integration')->group(function () {

        Route::prefix('/registration')->group(function () {
            Route::get('/registrations/event/{eventUid}/all', function () {
            });
            Route::get('/{registrationUid}/registration', function () {
            });
        });

        Route::prefix('/event')->group(function () {
            Route::get('/{eventUid}/event', function () {
            });

            Route::post('/' , [EventController::class, 'create']);
            Route::get('/all' , [EventController::class, 'all']);
            Route::put('/' , [EventController::class, 'update']);
            Route::get('/{eventUid}', [EventController::class, 'eventbyid']);
            Route::delete('/{eventUid}' , [EventController::class, 'delete']);

            // Route detail endpoints
            Route::get('/event/{event_uid}/route-details', [EventController::class, 'getRouteDetails']);
            Route::post('/event/{event_uid}/route-details', [EventController::class, 'updateRouteDetails']);
            Route::put('/event/{event_uid}/route-details', [EventController::class, 'updateRouteDetails']);
        });

        Route::prefix('/event-group')->group(function () {
            Route::post('/', [EventGroupController::class, 'create']);
            Route::put('/', [EventGroupController::class, 'update']);
            Route::get('/all', [EventGroupController::class, 'all']);
            Route::get('/{uid}', [EventGroupController::class, 'get']);
            Route::delete('/{uid}', [EventGroupController::class, 'delete']);
        });
    });

    Route::prefix('/artisan')->group(function () {

        Route::get('/migrate', function () {
            Artisan::call('migrate', ["--force" => true]);
            Artisan::call('app:country-update');
        });

        Route::get('/command/country/run', function () {
            Artisan::call('app:country-update');
        });

        Route::get('/command/cache/run', function () {
            Artisan::call('view:cache');
            Artisan::call('route:cache');
           Artisan::call('event:cache');
        });

        Route::get('/command/schedule/run', function () {
            Artisan::call('schedule:run');
        });
    });
});


//});
