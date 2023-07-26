<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\WarehouseController;
use App\Models\Warehouse;
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
  Route::get('products_warehouse/{id}', [WarehouseController::class, 'productsWarehouse']);
  Route::get('revenue_month/{id}', [WarehouseController::class, 'revenueByMonthWarehouse']);
  Route::get('revenue_week/{id}', [WarehouseController::class, 'revenueByWeekWarehouse']);
  Route::get('revenue_date/{id}', [WarehouseController::class, 'revenueByDateWarehouse']);
  Route::get('revenue_year/{id}', [WarehouseController::class, 'revenueByYearWarehouse']);
  Route::post('revenue_interval/{id}', [WarehouseController::class, 'revenueByInterval']);
  Route::get('products_infor/{id}', [WarehouseController::class, 'allProductsWarehouse']);
  Route::get('employees/{id}', [WarehouseController::class, 'getEmployee']);
  Route::post('warehouses_filter', [WarehouseController::class, 'warehouseFilter']);

 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::get('users_by_page',[AuthController::class, 'getUserByPage']);
  Route::get('all_employees',[EmployeeController::class, 'viewAllEmployee']);
  Route::get('employees',[EmployeeController::class, 'getEmployeeByPage']);
  Route::get('employee/{id}',[EmployeeController::class, 'getEmployeeById']);
  Route::post('update/employee/{id}',[AuthController::class, 'updateUser']);
  Route::get('delete/employee/{id}',[EmployeeController::class, 'destroy']);
  Route::get('user_employee/{id}',[EmployeeController::class, 'getEmployeeByUser']);
  Route::get('orders_employee_date/{id}',[EmployeeController::class, 'ordersCreatedEmp']);
  Route::get('orders_employee_week/{id}',[EmployeeController::class, 'ordersWeek']);
  Route::get('orders_employee_month/{id}',[EmployeeController::class, 'ordersMonth']);
  Route::get('orders_employee_year/{id}',[EmployeeController::class, 'ordersYear']);
  Route::get('orders_date_employee/{id}',[EmployeeController::class, 'ordersDateSuccess']);
  Route::post('orders_empoyee_date_interval/{id}',[EmployeeController::class, 'ordersIntervalEmp']);
  Route::get('orders_list_emp/{id}',[EmployeeController::class, 'ordersListEmp']);
  Route::post('update_employee/{id}',[EmployeeController::class, 'update']);
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
  Route::get('products',[ProductController::class, 'all']);
  Route::get('product_brands/{id}',[ProductController::class, 'getBrandByProId']);
  Route::get('product_brand_size/{id}/{brand_id}',[ProductController::class, 'getSizeByBrandPro']);
  Route::get('product_brand_size_color/{id}/{brand_id}/{size_id}',[ProductController::class, 'getColorByBraProSiz']);
  Route::get('product_brand_size_color_warehouse/{id}/{brand_id}/{size_id}/{color_id}',[ProductController::class, 'getWarehouseByProperties']);
  Route::get('product_brand_size_color_warehouse_agent/{id}/{brand_id}/{size_id}/{color_id}/{warehouse_id}',[ProductController::class, 'getAgentByProperties']);
  Route::get('get_discount/{id}/{brand_id}/{size_id}/{color_id}/{warehouse_id}/{agent_id}',[ProductController::class, 'getDiscount']);
  Route::get('view_properties/{id}/{warehouse_id}',[ProductController::class, 'getPropertiesAndWare']);
  Route::get('product_properties/{id}',[ProductController::class, 'getProperties']);
  Route::get('get_image/{id}',[ProductController::class, 'getImage']);
  Route::get('get_warehouses/{id}',[ProductController::class, 'getWarehouse']);
  Route::get('get_images/{id}',[ProductController::class, 'getAllImageById']);
  Route::get('product/{id}',[ProductController::class, 'getById']);
  Route::get('delete/product/{id}', [ProductController::class, 'destroy']);
  Route::get('product_colors/{id}',[ProductController::class, 'getColorByProductId']);
  Route::get('total_product',[ProductController::class, 'totalProduct']);
  Route::get('inventories',[ProductController::class, 'inventories']);
  Route::get('buy_most',[ProductController::class, 'productBuyMost']);
  Route::post('buy_most_interval',[ProductController::class, 'productBuyMostInterval']);
  Route::post('product_date_interval',[ProductController::class, 'productByDateInterval']);
  Route::get('product_date',[ProductController::class, 'productByDate']);
  Route::get('all_products_properties',[ProductController::class, 'allProductsProp']);
  Route::post('filter_product',[ProductController::class, 'filterProduct']);
  Route::post('filter_product_page',[ProductController::class, 'filterProductByPage']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_order', [OrderController::class, 'create']);
  Route::post('update/order/{id}', [OrderController::class, 'update']);
  Route::get('customers', [CustomerController::class, 'all']);
  Route::get('customer/{id}', [CustomerController::class, 'getById']);
  Route::get('view_orders', [OrderController::class, 'getByPage']);
  Route::get('order_customer/{id}', [OrderController::class, 'getCustomer']);
  Route::get('order_products/{id}', [OrderController::class, 'getProducts']);
  Route::get('order/{id}', [OrderController::class, 'getById']);
  Route::get('total_order', [OrderController::class, 'totalOrder']);
  Route::get('revenue', [OrderController::class, 'revenue']);
  Route::post('revenue_interval', [OrderController::class, 'revenueInterval']);
  Route::get('orders_date', [OrderController::class, 'orderDate']);
  Route::get('orders_week', [OrderController::class, 'orderByWeek']);
  Route::get('orders_month', [OrderController::class, 'orderByMonth']);
  Route::get('orders_year', [OrderController::class, 'orderByYear']);
  Route::post('orders_date_interval', [OrderController::class, 'orderDateInterval']);
  Route::post('orders_filter', [OrderController::class, 'orderFilter']);
  Route::get('received_orders', [OrderController::class, 'getReceivedOrder']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('add_agent', [AgentController::class, 'create']);
  Route::get('view_agents', [AgentController::class, 'getByPage']);
  Route::get('agent/{id}', [AgentController::class, 'getById']);
  Route::post('update/agent/{id}', [AgentController::class, 'update']);
  Route::get('delete/agent/{id}', [AgentController::class, 'destroy']);
  Route::get('agents', [AgentController::class, 'all']);
  Route::post('agents_filter',[AgentController::class, 'agentsFilter']);
 });

 Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::get('total_customer', [CustomerController::class, 'totalCus']);
  Route::get('view_customers', [CustomerController::class, 'getByPage']);
  Route::get('chart_customer', [CustomerController::class, 'customers']);
  Route::post('customers_buy_interval', [CustomerController::class, 'customerByMostByInterval']);
  Route::post('customers_number_interval', [CustomerController::class, 'cusNumberByInterval']);
  Route::get('order_by_cus/{id}', [CustomerController::class, 'getOrders']);
  Route::get('customers_month', [CustomerController::class, 'customerByMounth']);
  Route::get('customers_week', [CustomerController::class, 'customerByWeek']);
  Route::get('customers_year', [CustomerController::class, 'customerByYear']);
  Route::get('customers_date', [CustomerController::class, 'customerByDate']);
  Route::get('employees_users', [EmployeeController::class, 'getAllUserEmployee']);
 });

 