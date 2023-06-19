<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Import\ImportController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\Prescription\PrescriptionController;
use App\Http\Controllers\Setting\PermissionController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;


Route::post('/sign-up', [AuthController::class, 'signUp']); //sign up

Route::prefix('/sign-in')->group(function () {

    Route::post('/', [AuthController::class, 'signIn']); //log in

    // google login
    Route::get('/{channel}', [AuthController::class, 'channel']);
    Route::get('/{channel}/callback', [AuthController::class, 'channelCallback']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::middleware(['auth', 'role:superOperator|user'])->group(function () {

        // dashboard data
        Route::prefix('/dashboard')->group(function () {
            Route::get('', [DashboardController::class, 'index']);
        });

        // all import route
        Route::prefix('/import')->group(function () {
            Route::post('/medicine-data', [ImportController::class, 'importMedicineData']); // add prescription
        });

        // search medicine name
        Route::prefix('/medicine')->group(function () {
            Route::get('/search/{searchingBy}', [MedicineController::class, 'search']); // search medicine
        });

        // prescription form
        Route::prefix('/prescription')->group(function () {
            Route::post('/add', [PrescriptionController::class, 'addPrescription']); // add prescription
            Route::get('/edit/{medicineId}', [PrescriptionController::class, 'editPrescription']); // edit prescription)
            Route::get('/list', [PrescriptionController::class, 'prescriptionList']); // prescription list
            Route::put('/update', [PrescriptionController::class, 'updatePrescription']); // update prescription

            Route::get('{medicineId}/dosage', [PrescriptionController::class, 'dosageDetails']); // dosage prescription
        });

        // profile-management
        Route::prefix('/profile')->group(function () {
            Route::get('', [UserProfileController::class, 'show']);
            Route::put('/update-profile', [UserProfileController::class, 'updateProfile']);
            Route::put('/update-password', [UserProfileController::class, 'updatePassword']);
        });

        // permission-management
        Route::prefix('/permission')->group(function () {
            Route::get('/index', [PermissionController::class, 'index']);
            Route::post('/store', [PermissionController::class, 'store']);

            Route::get('{id}/edit', [PermissionController::class, 'edit']);
            Route::put('{id}/update', [PermissionController::class, 'update']);
        });

        // role-management
        Route::prefix('/role')->group(function () {
            Route::get('/index', [RoleController::class, 'index']);
            Route::post('/store', [RoleController::class, 'store']);

            Route::get('{id}/edit', [RoleController::class, 'edit']);
            Route::put('{id}/update', [RoleController::class, 'update']);
        });

        // user-management
        Route::prefix('/user')->group(function () {
            Route::get('/index', [UserController::class, 'index']);
            Route::post('/store', [UserController::class, 'store']);
            Route::get('{id}/edit', [UserController::class, 'edit']);
            Route::put('{id}/update', [UserController::class, 'update']);
        });
    });
});

