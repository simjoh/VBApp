<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CheckoutController;
use App\Models\Event;

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

Route::get('/', function () {
//    echo __('I love programming.');
    return view('welcome');
})->name('welcome');


Route::get('/events', function (Event $event) {
    return $event->title;
});
Route::get('/events/{uid}/register', function (string $uid) {
    return view('registrations.show')->with(['countries' => \App\Models\Country::all()->sortByDesc("country_name_en")]); // Event::find($uid)->title;
});

Route::post('/events/{uid}/register', [RegistrationController::class, 'create']);
Route::post('/events/{uid}/reserve', [RegistrationController::class, 'reserve']);
Route::get('/events/{uid}/registration/{regsitrationUid}/complete', [RegistrationController::class, 'complete']);
Route::get('/events/{uid}/registration/{regsitrationUid}/getregitration', [RegistrationController::class, 'existingregistration']);
Route::put('/events/{uid}/registration/{regsitrationUid}/update', [RegistrationController::class, 'update']);

Route::get('/checkout/index', [CheckoutController::class, 'index'])->name("checkout");
Route::post('/checkout/create', [CheckoutController::class, 'create']);
Route::get('/checkout/success', [CheckoutController::class, 'success']);
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel']);

Route::get('lang/{lang}', [LocaleController::class, 'switchLang']);
