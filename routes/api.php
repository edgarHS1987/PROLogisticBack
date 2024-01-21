<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;

use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\DriversAddressController;
use App\Http\Controllers\DriversDocumentsController;
use App\Http\Controllers\DriversDocumentsImagesController;
use App\Http\Controllers\DriversVehiclesController;
use App\Http\Controllers\ReportsController;

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
    
    Route::post('drivers/create', [DriversController::class, 'store']);
    Route::get('drivers', [DriversController::class, 'index']);
    Route::get('drivers/{id}', [DriversController::class, 'edit']);
    Route::get('drivers/available/days', [DriversController::class, 'availableDays']);
    Route::post('drivers/available/days', [DriversController::class, 'availableDaysStore']);

    Route::get('drivers/address/{id}', [DriversAddressController::class, 'edit']);
    Route::post('drivers/address/create', [DriversAddressController::class, 'store']);
    //Route::put('drivers/address/{id}', [DriversAddressController::class, 'edit']);

    Route::post('drivers/docs/uploadDoc/', [DriversDocumentsController::class, 'subir']);
    Route::post('drivers/docs/clearFolder', [DriversDocumentsController::class, 'deleteFolder']);
    Route::post('drivers/docs/create', [DriversDocumentsController::class, 'store']);
    //Route::post('drivers/docs/update{id}', [DriversDocumentsController::class, 'update']);
    Route::get('drivers/docs/{id}', [DriversDocumentsController::class, 'edit']);

    Route::get('drivImages/listaDocuments/{tipo}/{id}', [DriversDocumentsImagesController::class, 'listDocuments']);
    Route::get('drivImages/oneDocument/{file}/{tipo}/{id}', [DriversDocumentsImagesController::class, 'getDoc']);
    Route::get('drivImages/allDocs/{tipo}/{id}', [DriversDocumentsImagesController::class, 'listDocsVisor']);
    Route::get('drivImages/visorDoc/{file}/{tipo}/{id}', [DriversDocumentsImagesController::class, 'getDocumentVisor']);

    Route::post('drivers/vehicle/create', [DriversVehiclesController::class, 'store']);
    Route::get('drivers/vehicle/all', [DriversVehiclesController::class, 'index']);
    Route::get('drivers/vehicle/{id}', [DriversVehiclesController::class, 'edit']);
    Route::post('drivers/vehicle/update', [DriversVehiclesController::class, 'update']);

    Route::get('reports/workDays/{fechaIn}/{fechaFn}', [ReportsController::class, 'workDays']);
    Route::get('reports/servicesAsigned/{fechaIn}/{fechaFn}', [ReportsController::class, 'servicesAsigned']);
    Route::get('reports/servicesStatus/{fechaIn}/{fechaFn}', [ReportsController::class, 'statusServices']);

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

    Route::post('zones/assign/driver', [ZonesController::class, 'assignDriver']);
    Route::get('zones/byClient/{id}', [ZonesController::class, 'byClient']);
    Route::get('zones/configured/{id}', [ZonesController::class, 'configured']);
    Route::post('zones/configuring', [ZonesController::class, 'configuring']);
    Route::get('zones/drivers/{id}', [ZonesController::class, 'zonesDrivers']);
    Route::get('zones/unsignedDrivers', [ZonesController::class, 'unsignedDriver']);
    Route::post('zones/verify', [ZonesController::class, 'verifyIfExist']);

    Route::get('warehouses/show/{id}', [WarehousesController::class, 'show']);
    Route::get('warehouses/test/{id}', [WarehousesController::class, 'test']);

    Route::post('services', [ServicesController::class, 'store']);
    Route::post('services/assign', [ServicesController::class, 'assignToDriver']);
    Route::get('services/assigned', [ServicesController::class, 'assigned']);
    Route::delete('services/delete/{id}', [ServicesController::class, 'delete']);
    Route::get('services/details/{id}', [ServicesController::class, 'details']);
    Route::get('services/list/{id}/{date}', [ServicesController::class, 'list']);
    Route::post('services/start/charge', [ServicesController::class, 'startCharge']);
    Route::post('services/end/charge', [ServicesController::class, 'endCharge']);
    Route::post('services/start/deliver/{id}', [ServicesController::class, 'startDeliver']);
    Route::get('services/show/{id}', [ServicesController::class, 'show']);
    Route::post('services/unsigned', [ServicesController::class, 'unsignedByClient']);
    Route::get('services/unsigned/{id}', [ServicesController::class, 'totalUnsignedByClient']);
    Route::get('services/location/{id}', [ServicesController::class, 'location']);
    Route::put('services/update/location', [ServicesController::class, 'locationUpdate']);
    
    
    
});