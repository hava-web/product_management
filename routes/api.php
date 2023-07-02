<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SizeController;
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

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_category', [CategoryController::class, 'create']);
  Route::get('view_categories', [CategoryController::class, 'getCategoryByPage']);
  Route::get('all_categories', [CategoryController::class, 'all']);
  Route::get('category/{id}', [CategoryController::class, 'getCategoryById']);
  Route::post('update/category/{id}', [CategoryController::class, 'update']);
  Route::get('delete/category/{id}', [CategoryController::class, 'remove']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_color', [ColorController::class, 'create']);
  Route::get('view_colors', [ColorController::class, 'getByPage']);
  Route::get('all_colors', [ColorController::class, 'getAll']);
  Route::get('color/{id}', [ColorController::class, 'getColor']);
  Route::post('update/color/{id}', [ColorController::class, 'update']);
  Route::get('delete/color/{id}', [ColorController::class, 'destroy']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_brand', [BrandController::class, 'create']);
  Route::get('view_brands', [BrandController::class, 'getByPage']);
  Route::get('/all_brands', [BrandController::class, 'all']);
  Route::get('brand/{id}', [BrandController::class, 'getById']);
  Route::post('update/brand/{id}', [BrandController::class, 'update']);
  Route::get('delete/brand/{id}', [BrandController::class, 'destroy']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_size', [SizeController::class, 'create']);
  Route::get('view_sizes', [SizeController::class, 'getByPage']);
  Route::get('/all_sizes', [SizeController::class, 'all']);
  Route::get('size/{id}', [SizeController::class, 'getById']);
  Route::post('update/size/{id}', [SizeController::class, 'update']);
  Route::get('delete/size/{id}', [SizeController::class, 'destroy']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_product', [ProductController::class, 'create']);
  Route::post('update/product/{id}', [ProductController::class, 'update']);
  Route::get('view_products',[ProductController::class, 'getByPage']);
  Route::get('view_properties/{id}/{warehouse_id}',[ProductController::class, 'getPropertiesAndWare']);
  Route::get('product_properties/{id}',[ProductController::class, 'getProperties']);
  Route::get('get_image/{id}',[ProductController::class, 'getImage']);
  Route::get('get_warehouses/{id}',[ProductController::class, 'getWarehouse']);
  Route::get('get_images/{id}',[ProductController::class, 'getAllImageById']);
  Route::get('product/{id}',[ProductController::class, 'getById']);
  Route::get('delete/product/{id}', [ProductController::class, 'destroy']);
  Route::get('product_colors/{id}',[ProductController::class, 'getColorByProductId']);
 });

 