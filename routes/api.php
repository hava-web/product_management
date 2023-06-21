<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
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
    
    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::post('register', [AuthController::class, 'register']);
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
      Route::get('all_users', [AuthController::class, 'getAllUsers']);
    });
});
 Route::group(['middleware' => 'auth:sanctum'], function() {
  Route::post('add_wareshouse', [WarehouseController::class, 'addWarehouse']);
  Route::get('view_warehouse', [WarehouseController::class, 'getWarehouseByPage']);
  Route::get('all_warehouse', [WarehouseController::class, 'getAllWarehouse']);
  Route::get('warehouse/{id}', [WarehouseController::class, 'getWarehouseById']);
  Route::post('update/warehouse/{id}', [WarehouseController::class, 'updateWarehouse']);
  Route::get('delete/warehouse/{id}', [WarehouseController::class, 'deleteWarehouse']);
  Route::get('user/{id}', [AuthController::class, 'getUserById']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::get('users_by_page',[AuthController::class, 'getUserByPage']);
  Route::get('all_employees',[EmployeeController::class, 'viewAllEmployee']);
  Route::get('employees',[EmployeeController::class, 'getEmployeeByPage']);
  Route::get('employee/{id}',[EmployeeController::class, 'getEmployeeById']);
  Route::post('update/employee/{id}',[AuthController::class, 'updateUser']);
  Route::get('delete/employee/{id}',[EmployeeController::class, 'destroy']);
 });

 