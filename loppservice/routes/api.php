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
use App\Http\Controllers\TestController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\ErrorEventController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\EventGroupController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeSyncWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware('throttle:60,1')->group(function () {

Route::get('/api/simple-test', [TestController::class, 'simpleTest']);


Route::prefix('/api')->group(function () {


    Route::get('/ping' , [IntegrationController::class, 'testCyclingAppIntegration']);

    Route::post('/transfer' , [IntegrationController::class, 'publishToCyclingApp']);

    Route::get('/pingapikey', [TestController::class, 'pingApiKey'])->middleware('apikey');

    Route::get('/pingjwt', [TestController::class, 'pingJwt']);

    Route::get('/testjwt', [TestController::class, 'testJwt']);

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

        // Error events endpoints
        Route::get('/error-events', [ErrorEventController::class, 'getErrorEvents'])->name('api.error_events');
        Route::get('/failed-publish-events', [ErrorEventController::class, 'getFailedPublishEvents'])->name('api.failed_publish_events');
        Route::post('/retry-publish-event/{errorEventUid}', [ErrorEventController::class, 'retryPublishEvent'])->name('api.retry_publish_event');
        Route::post('/retry-all-publish-events', [ErrorEventController::class, 'retryAllPublishEvents'])->name('api.retry_all_publish_events');

        // Integration endpoints
        Route::get('/published-events-count', [ToolController::class, 'getPublishedEventsCount'])->name('api.published_events_count');

        // Stripe API endpoints
        Route::prefix('/stripe')->group(function () {
            Route::get('/status', [StripeController::class, 'getStatus'])->name('api.stripe.status');
            Route::get('/products', [StripeController::class, 'getProducts'])->name('api.stripe.products');
            Route::get('/products/{productId}', [StripeController::class, 'getProduct'])->name('api.stripe.product');
            Route::get('/prices', [StripeController::class, 'getDefaultPrices'])->name('api.stripe.prices');
            Route::get('/balance', [StripeController::class, 'getBalance'])->name('api.stripe.balance');

            // Transaction endpoints
            Route::get('/transactions/counts', [StripeController::class, 'getTransactionCounts'])->name('api.stripe.transaction_counts');
            Route::get('/transactions/recent', [StripeController::class, 'getRecentTransactions'])->name('api.stripe.recent_transactions');

            // Product management endpoints
            Route::post('/products', [StripeController::class, 'createProduct'])->name('api.stripe.create_product');
            Route::put('/products/{productId}', [StripeController::class, 'updateProduct'])->name('api.stripe.update_product');
            Route::delete('/products/{productId}', [StripeController::class, 'deleteProduct'])->name('api.stripe.delete_product');
            Route::put('/products/{productId}/archive', [StripeController::class, 'archiveProduct'])->name('api.stripe.archive_product');
            Route::put('/products/{productId}/restore', [StripeController::class, 'restoreProduct'])->name('api.stripe.restore_product');

            // Price management endpoints
            Route::get('/products/{productId}/prices', [StripeController::class, 'getProductPrices'])->name('api.stripe.product_prices');
            Route::post('/products/{productId}/prices', [StripeController::class, 'createPrice'])->name('api.stripe.create_price');
            Route::put('/products/{productId}/prices/default', [StripeController::class, 'setDefaultPrice'])->name('api.stripe.set_default_price');
            Route::post('/products/{productId}/prices/create-and-set-default', [StripeController::class, 'createAndSetDefaultPrice'])->name('api.stripe.create_and_set_default_price');
        });

        // Stripe Sync Manual endpoints (for testing/debugging)
        Route::prefix('/syncing')->group(function () {
            Route::post('/manual-sync', [StripeSyncWebhookController::class, 'manualSync'])->name('api.stripe.manual_sync');
            Route::get('/test', function () {
                return response()->json(['message' => 'Manual sync endpoint is working!']);
            });
        });
    });

    Route::prefix('/artisan')->group(function () {
        Route::get('/migrate', [DeveloperController::class, 'migrate']);
        Route::get('/command/country/run', [DeveloperController::class, 'countryUpdate']);
        Route::get('/command/cache/run', [DeveloperController::class, 'cacheRun']);
        Route::get('/command/schedule/run', [DeveloperController::class, 'scheduleRun']);
    });
});



//});
