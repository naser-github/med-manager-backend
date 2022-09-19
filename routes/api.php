<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\Prescription\PrescriptionController;
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

        Route::prefix('/prescription')->group(function () {
            Route::post('/add', [PrescriptionController::class, 'addPrescription']); // add prescription
            Route::get('/list', [PrescriptionController::class, 'prescriptionList']); // prescription list
            Route::put('/update', [PrescriptionController::class, 'updatePrescription']); // update prescription
        });

        Route::prefix('/medicine')->group(function () {
            Route::get('/search/{searchingBy}', [MedicineController::class, 'search']); // search medicine
        });

    });
});

