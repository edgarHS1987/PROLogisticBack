<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriversDocumentsImagesController;

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
    return view('welcome');
});

// Route::group(['middleware' => ['cors']], function () {
    // Route::get('url', [DriversDocumentsImagesController::class, 'getDoc']);
    // Route::get('download', [DriversDocumentsImagesController::class, 'download'])->name('download');

// });


