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
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware('throttle:60,1')->group(function () {

Route::get('/api/simple-test', function () {
    return response()->json(['message' => 'Simple test route works']);
});

Route::prefix('/api')->group(function () {


    Route::get('/ping' , [ToolController::class, 'testappintegration']);

    Route::post('/transfer' , [ToolController::class, 'publishToCyclingappIfNotAlreadyRegister']);

    Route::get('/pingapikey', ['middleware' => ['apikey',], function () {
        return 'Testar kontroll av apinyckel';
    }]);

    Route::get('/pingjwt', function () {
        return response()->json([
            'message' => 'JWT validation successful',
            'user_id' => request()->attributes->get('current_user_id'),
            'organizer_id' => request()->attributes->get('current_organizer_id'),
            'roles' => request()->attributes->get('current_user_roles', []),
            'timestamp' => now()->toISOString()
        ]);
    });

    Route::get('/testjwt', function () {
        return response()->json([
            'message' => 'Test route without JWT middleware',
            'timestamp' => now()->toISOString()
        ]);
    });

    Route::prefix('/integration')->middleware('apikey')->group(function () {

        Route::prefix('/registration')->group(function () {
            Route::get('/registrations/event/{eventUid}/all', function () {
            });
            Route::get('/{registrationUid}/registration', function () {
            });
            Route::delete('/{registrationUid}', [RegistrationController::class, 'delete'])->name('api.registration.delete');
        });

        Route::prefix('/person')->group(function () {
            Route::get('/{personUid}', [PersonController::class, 'get'])->name('api.person.get');
            Route::delete('/forget', [PersonController::class, 'forget'])->name('api.person.forget');
        });

        Route::prefix('/event')->group(function () {
            Route::get('/{eventUid}/event', function () {
            });

            Route::post('/' , [EventController::class, 'create'])->name('api.events.create');
            Route::get('/all' , [EventController::class, 'all'])->name('api.events.index');
            Route::get('/by-type' , [EventController::class, 'getByEventType'])->name('api.events.by_type');
            Route::put('/{eventUid}' , [EventController::class, 'update'])->name('api.events.update');
            Route::get('/{eventUid}', [EventController::class, 'eventbyid'])->name('api.events.show');
            Route::delete('/{eventUid}' , [EventController::class, 'delete'])->name('api.events.delete');

            // Route detail endpoints
            Route::get('/event/{event_uid}/route-details', [EventController::class, 'getRouteDetails'])->name('api.events.route_details');
            Route::post('/event/{event_uid}/route-details', [EventController::class, 'updateRouteDetails'])->name('api.events.update_route_details');
            Route::put('/event/{event_uid}/route-details', [EventController::class, 'updateRouteDetails']);

            // Statistics endpoints
            Route::get('/{eventUid}/stats', [StatsController::class, 'getEventStats'])->name('api.events.stats');
            Route::get('/{eventUid}/optional-products', [StatsController::class, 'getEventOptionalProducts'])->name('api.events.optional_products');
            Route::get('/{eventUid}/registrations', [StatsController::class, 'getEventRegistrations'])->name('api.events.registrations');
        });

        Route::prefix('/event-group')->group(function () {
            Route::post('/', [EventGroupController::class, 'create'])->name('api.event_groups.create');
            Route::put('/{uid}', [EventGroupController::class, 'update'])->name('api.event_groups.update');
            Route::get('/all', [EventGroupController::class, 'all'])->name('api.event_groups.index');
            Route::get('/{uid}', [EventGroupController::class, 'get'])->name('api.event_groups.show');
            Route::delete('/{uid}', [EventGroupController::class, 'delete'])->name('api.event_groups.delete');
        });

        // Organizer API routes
        Route::prefix('/organizers')->group(function () {
            Route::get('/', [OrganizerController::class, 'index'])->name('api.organizers.index');
            Route::post('/', [OrganizerController::class, 'store'])->name('api.organizers.store');
            Route::get('/{id}', [OrganizerController::class, 'show'])->name('api.organizers.show');
            Route::put('/{id}', [OrganizerController::class, 'update'])->name('api.organizers.update');
            Route::delete('/{id}', [OrganizerController::class, 'destroy'])->name('api.organizers.destroy');
            Route::get('/{id}/events', [OrganizerController::class, 'events'])->name('api.organizers.events');
        });

        // Club API routes
        Route::prefix('/clubs')->group(function () {
            Route::get('/', [ClubController::class, 'index'])->name('api.clubs.index');
            Route::post('/', [ClubController::class, 'store'])->name('api.clubs.store');
            Route::get('/{id}', [ClubController::class, 'show'])->name('api.clubs.show');
            Route::put('/{id}', [ClubController::class, 'update'])->name('api.clubs.update');
            Route::delete('/{id}', [ClubController::class, 'destroy'])->name('api.clubs.destroy');
        });

        // Non-participant optionals endpoint
        Route::get('/non-participant-optionals', [StatsController::class, 'getNonParticipantOptionals'])->name('api.non_participant_optionals');
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
