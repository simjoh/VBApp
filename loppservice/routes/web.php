<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NoRegisterCheckoutController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StartlistController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\StripeSyncWebhookController;
use App\Models\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('login',array('as'=>'login',function(){
//    return view('login');
//}));

Route::get('/', function () {
//    echo __('I love programming.');
    return view('welcome');
})->name('welcome');


Route::get('/events', function (Event $event) {
    return $event->title;
});

Route::get('/events' ,[EventController::class, 'index']);

Route::get('/tool' ,[ToolController::class, 'index']);
Route::post('/tool', [ToolController::class, 'run']);
Route::post('/tool/event/{eventUid}', [ToolController::class, 'publishToCyclingappIfNotAlreadyRegister']);



Route::get('/events/{uid}/register', [RegistrationController::class, 'index'])->name('register');
Route::post('/events/{uid}/register', [RegistrationController::class, 'create']);
Route::post('/events/{uid}/reserve', [RegistrationController::class, 'reserve']);

Route::get('/events/{uid}/registration/{regsitrationUid}/complete', [RegistrationController::class, 'complete']);
Route::get('/events/{uid}/registration/{registrationUid}/getregitration', [RegistrationController::class, 'existingregistration']);
Route::put('registration.update', [RegistrationController::class, 'update']);
Route::post('registration.create', [RegistrationController::class, 'create']);
Route::post('registration.msrreserve', [RegistrationController::class, 'msrreserve'])->name('registration.msrreserve');
Route::post('registration.msrcomplete', [RegistrationController::class, 'msrcomplete'])->name('registration.msrcomplete');
Route::get('/events/{uid}/registration/{registration_uid}/msrcomplete', [RegistrationController::class, 'msrshowcomplete'])->name('registration.msrshowcomplete');

Route::get('/checkout/create', [CheckoutController::class, 'create'])->name("checkout");
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkoutsuccess');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel']);
Route::post('/payments/events', [WebhookController::class, 'index']);

// Stripe sync webhook endpoints (for product/price synchronization)
Route::post('/syncing/events', [StripeSyncWebhookController::class, 'handleWebhook']);
Route::post('/api/syncing/events', [StripeSyncWebhookController::class, 'handleWebhook']);

Route::get('/startlist/event/{eventuid}/showall', [StartlistController::class, 'startlistFor']);

Route::get('lang/{lang}', [LocaleController::class, 'switchLang']);


Route::get('/event/{uid}/order', [OrderController::class, 'index']);
Route::post('/event/{uid}/order', [OrderController::class, 'placeorder']);
Route::get('/optionals/checkout/create', [NoRegisterCheckoutController::class, 'create'])->name("noregistercheckout");
Route::get('/optionals/checkout/success', [NoRegisterCheckoutController::class, 'success']);
Route::get('/optionals/checkout/cancel', [NoRegisterCheckoutController::class, 'cancel']);


Route::get('/profile', function () {
    // Only authenticated users may access this route...
})->middleware('auth.basic');

Route::get('/registration/{uid}', [App\Http\Controllers\RegistrationController::class, 'index'])
    ->name('registration.index');

Route::get('/events/{uid}/login', [EventController::class, 'getLogin'])
    ->name('event.login');
