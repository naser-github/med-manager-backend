<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//sign up
Route::post('/sign-up', [AuthController::class, 'signUp']);

Route::prefix('/sign-in')->group(function () {
    //log in
    Route::post('/', [AuthController::class, 'signIn']);

    // google login
    Route::get('/{channel}', [AuthController::class, 'channel']);
    Route::get('/{channel}/callback', [AuthController::class, 'channelCallback']);
});


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::middleware(['auth', 'role:superOperator|user'])->group(function () {

        // add prescription
        Route::post('/add-prescription', 'App\Http\Controllers\PrescriptionController@addPrescription');

        // prescription list
        Route::get('/prescription-list', 'App\Http\Controllers\PrescriptionController@prescriptionList');

        // update prescription
        Route::put('/update-prescription', 'App\Http\Controllers\PrescriptionController@updatePrescription');

    });
});

//        Route::prefix('/application')->group(function () {
//        });


