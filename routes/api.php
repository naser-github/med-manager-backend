<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\Prescription\PrescriptionController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\UserProfileController;
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

        Route::prefix('/dashboard')->group(function () {
            Route::get('', [DashboardController::class, 'index']); // add prescription
        });

        Route::prefix('/medicine')->group(function () {
            Route::get('/search/{searchingBy}', [MedicineController::class, 'search']); // search medicine
        });

        Route::prefix('/prescription')->group(function () {
            Route::post('/add', [PrescriptionController::class, 'addPrescription']); // add prescription
            Route::get('/edit/{medicineId}', [PrescriptionController::class, 'editPrescription']); // edit prescription)
            Route::get('/list', [PrescriptionController::class, 'prescriptionList']); // prescription list
            Route::put('/update', [PrescriptionController::class, 'updatePrescription']); // update prescription

            Route::get('{medicineId}/dosage', [PrescriptionController::class, 'dosageDetails']); // dosage prescription
        });

        Route::prefix('/profile')->group(function () {
            Route::get('', [UserProfileController::class, 'show']);
            Route::put('/update-profile', [UserProfileController::class, 'updateProfile']);
            Route::put('/update-password', [UserProfileController::class, 'updatePassword']);
        });

        Route::prefix('/role')->group(function () {
            Route::get('/index', [RoleController::class, 'index']);
        });

        Route::prefix('/user')->group(function () {
            Route::get('/index', [UserController::class, 'index']);
            Route::post('/store', [UserController::class, 'store']);
            Route::get('{id}/edit', [UserController::class, 'edit']);
            Route::put('{id}/update', [UserController::class, 'update']);
        });

    });
});

