<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
      Route::get('all_users', [AuthController::class, 'getAllUsers']);
    });
});
 Route::group(['middleware' => 'auth:sanctum'], function() {
  Route::post('add_wareshouse', [WarehouseController::class, 'addWarehouse']);
  Route::get('view_warehouse', [WarehouseController::class, 'getWarehouseByPage']);
 });