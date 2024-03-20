<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NoRegisterCheckoutController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StartlistController;
use App\Http\Controllers\WebhookController;
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
//Route::get('/events/{uid}/register', function (string $uid) {
//   $count = Registration::all()->count();
//   if($count >= 200){
//       return Redirect::back()->withErrors(['msg' => 'Event is full']);
//   }
//    return view('registrations.show')->with(['countries' => Country::all()->sortByDesc("country_name_en"), 'years' => range(date('Y'), 1950)]); // Event::find($uid)->title;
//});

Route::get('/events/{uid}/register', [RegistrationController::class, 'index']);
Route::post('/events/{uid}/register', [RegistrationController::class, 'create']);
Route::post('/events/{uid}/reserve', [RegistrationController::class, 'reserve']);
Route::get('/events/{uid}/registration/{regsitrationUid}/complete', [RegistrationController::class, 'complete']);
Route::get('/events/{uid}/registration/{registrationUid}/getregitration', [RegistrationController::class, 'existingregistration']);
Route::put('registration.update', [RegistrationController::class, 'update']);

Route::get('/checkout/create', [CheckoutController::class, 'create'])->name("checkout");
Route::get('/checkout/success', [CheckoutController::class, 'success']);
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel']);
Route::post('/payments/events', [WebhookController::class, 'index']);

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
