<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::get('/ping', function () {
    // Matches The "/admin/users" URL
    return 'ping in group';
});

Route::prefix('/api')->group(function () {

    Route::get('/ping', function () {
        // Matches The "/admin/users" URL
        return 'ping in group';
    });



    Route::get('/pingapikey', ['middleware' => ['apikey',], function () {
        return 'User ';
    }]);



    Route::prefix('/pingtest')->group(function () {

        Route::get('/ping', function () {
            // Matches The "/admin/users" URL
            try {
                $conn = DB::connection()->getPdo();
            } catch (\Exception $e) {
                die("Could not connect to the database.  Please check your configuration. error:" . $e);
            }
        });
    });
});
