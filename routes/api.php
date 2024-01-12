<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;

use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;

use App\Http\Controllers\ClientsController;

use App\Http\Controllers\StatesController;
use App\Http\Controllers\MunicipalitiesController;
use App\Http\Controllers\ZonesController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\ServicesController;
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

Route::group(['prefix'=>'auth'], function(){
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware'=>'auth:api'], function(){
        Route::get('logout', [AuthController::class, 'logout']);
    });
});

Route::group(['middleware'=>'auth:api'], function(){
    Route::get('permissions', [PermissionsController::class, 'index']);
    Route::get('permissions/assigned/{id}', [PermissionsController::class, 'permissionsAssignedToRole']);
    Route::post('permissions/assign', [PermissionsController::class, 'assign']);
    Route::post('permissions/design', [PermissionsController::class, 'design']);

    Route::get('roles', [RolesController::class, 'index']);
    Route::get('roles/{id}', [RolesController::class, 'edit']);
    Route::post('roles', [RolesController::class, 'store']);
    Route::put('roles/{id}', [RolesController::class, 'update']);
    Route::delete('roles/{id}', [RolesController::class, 'delete']);

    Route::get('users', [UsersController::class, 'index']);
    Route::get('users/{id}', [UsersController::class, 'edit']);
    Route::post('users', [UsersController::class, 'store']);
    Route::put('users/{id}', [UsersController::class, 'update']);
    Route::delete('users/{id}', [UsersController::class, 'delete']);
    Route::patch('users/reset/password', [UsersController::class, 'resetPassword']);
    
    Route::get('states', [StatesController::class, 'index']);
    Route::post('states', [StatesController::class, 'store']);
    Route::post('states/verify', [StatesController::class, 'verify']);
    Route::get('states/list', [StatesController::class, 'states']);
    
    Route::put('municipalities', [MunicipalitiesController::class, 'update']);
    Route::get('municipalities/list/{id}', [MunicipalitiesController::class, 'municipalities']);
    Route::post('municipality/verify', [MunicipalitiesController::class, 'verify']);
    Route::get('municipalities/show/{id}', [MunicipalitiesController::class, 'show']);

    Route::get('clients', [ClientsController::class, 'index']);
    Route::get('clients/list', [ClientsController::class, 'list']);

    Route::get('zones/byClient/{id}', [ZonesController::class, 'byClient']);
    Route::post('zones/verify', [ZonesController::class, 'verifyIfExist']);
    Route::post('zones/configuring', [ZonesController::class, 'configuring']);

    Route::get('warehouses/show/{id}', [WarehousesController::class, 'show']);

    Route::post('services/unsigned', [ServicesController::class, 'unsignedByClient']);
    Route::get('services/unsigned/{id}', [ServicesController::class, 'totalUnsignedByClient']);
    Route::post('services', [ServicesController::class, 'store']);
    Route::delete('services/{id}', [ServicesController::class, 'delete']);
});