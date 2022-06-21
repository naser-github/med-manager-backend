<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//log in
Route::post('/sign-in', 'App\Http\Controllers\LoginController@signIn');

//sign up
Route::post('/sign-up', 'App\Http\Controllers\UserManagement\UserController@store');

Route::group(['middleware' => 'auth:sanctum'], function () {

//    Route::middleware(['auth', 'role:superOperator|user'])->group(function () {

        // add medicines
        Route::post('/add-medicine', 'App\Http\Controllers\PrescriptionController@addPrescription');

//    });
});

//        Route::prefix('/application')->group(function () {
//        });


