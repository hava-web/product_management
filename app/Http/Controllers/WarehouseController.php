<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function addWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'manager' => 'int',
            'city' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string',
        ]);

        $warehouse = new Warehouse([
            'name'  => $request->name,
            'manager' => $request->manager,
            'city' => $request->city,
            'status' => $request->status,
            'address' => $request->address
        ]);

        if ($warehouse->save()) {
            return response()->json($warehouse, 201);
        } else {
            return response()->json(['error' => 'Provide proper details']);
        }
    }

    public function getWarehouseByPage()
    {
        $warehouses = Warehouse::paginate(5);
        return response()->json($warehouses);
    }

    public function getAllWarehouse()
    {
        $warehouses = Warehouse::all();
        return $warehouses;
    }

    public function getWarehouseById($id)
    {
        $warehouse = Warehouse::where('id', $id)->first();
        if ($warehouse) {
            return response()->json($warehouse);
        } else {
            return response()->json([
                "message" => "Warehouse does not exits",
            ], 404);
        }
    }

    public function updateWarehouse(Request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|string',
            'manager' => 'required|int',
            'city' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string',
        ]);
        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            $warehouse->name = $validatedData['name'];
            $warehouse->manager = $validatedData['manager'];
            $warehouse->city = $validatedData['city'];
            $warehouse->status = $validatedData['status'];
            $warehouse->address = $validatedData['address'];
            $warehouse->save();
            if ($warehouse->save()) {
                return response()->json([
                    'message' => 'Warehouse updated successfully',
                    'data' => $warehouse
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Warehouse does not exits'
            ], 404);
        }
    }

    public function getEmployee($id)
    {
        $employees = DB::table('employees AS e')
            ->join('users AS u', 'e.user_id', '=', 'u.id')
            ->select('e.id', 'e.firstname', 'e.lastname', 'e.image', 'e.date_of_birth', 'e.phone', 'u.email')
            ->where('e.warehouse_id', $id)
            ->get();
        return response()->json($employees);
    }

    public function productsWarehouse($id)
    {
        $products = DB::table('products as p')
            ->join('product_properties as pp', 'p.id', '=', 'pp.product_id')
            ->join('brands as b', 'pp.brand_id', '=', 'b.id')
            ->join('sizes as s', 'pp.size_id', '=', 's.id')
            ->join('colors as c', 'pp.color_id', '=', 'c.id')
            ->where('pp.warehouse_id', '=', $id)
            ->groupBy('pp.brand_id', 'pp.size_id', 'pp.color_id')
            ->selectRaw("CONCAT(p.name, '-', s.name, '-', c.name) AS x,  CAST(SUM(pp.quantity) AS UNSIGNED) AS y")
            ->get();
        return response()->json($products);
    }

    public function revenueByMonthWarehouse($id)
    {
        $data = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->select(DB::raw("MONTHNAME(od.created_at) AS x"), DB::raw('SUM(od.price) AS y'))
            ->where('o.status', 'Received')
            ->where('od.warehouse_id', $id)
            ->whereYear('od.created_at', '=', date('Y'))
            ->groupBy(DB::raw('MONTH(od.created_at)'))
            ->get();
        return response()->json($data);
    }

    public function revenueByWeekWarehouse($id)
    {
        $results = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->select(DB::raw("CONCAT('Week ', WEEK(od.created_at)) AS x"), DB::raw('SUM(od.price) AS y'))
            ->where('o.status', 'Received')
            ->where('od.warehouse_id', $id)
            ->whereYear('od.created_at', '=', date('Y'))
            ->groupBy('x')
            ->get();
        return response()->json($results);
    }

    public function revenueByDateWarehouse($id)
    {
        $data = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(DB::raw('DATE(orders.created_at) AS x'), DB::raw('CAST(SUM(order_details.price) AS UNSIGNED) AS y'))
            ->where('orders.status', 'Received')
            ->where('order_details.warehouse_id', $id)
            ->groupBy('x')
            ->get();
        return response()->json($data);
    }

    public function revenueByYearWarehouse($id)
    {
        $data = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(DB::raw('YEAR(order_details.created_at) AS x'), DB::raw('CAST(SUM(order_details.price) AS UNSIGNED) AS y'))
            ->where('orders.status', 'Received')
            ->where('order_details.warehouse_id', $id)
            ->groupBy('x')
            ->get();
        return response()->json($data);
    }

    public function revenueByInterval(Request $request, $id)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $data = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->select(DB::raw('DATE(od.created_at) AS x'), DB::raw('CAST(SUM(od.price) AS UNSIGNED) AS y'))
            ->where('o.status', 'Received')
            ->where('od.warehouse_id', $id)
            ->whereBetween('od.created_at', [$request->from, $request->to])
            ->groupBy('x')
            ->get();
        return response()->json($data);
    }

    public function allProductsWarehouse($id)
    {
        $results = DB::table('product_properties as pp')
            ->join('products as p', 'p.id', '=', 'pp.product_id')
            ->join('brands as b', 'b.id', '=', 'pp.brand_id')
            ->join('sizes as s', 's.id', '=', 'pp.size_id')
            ->join('colors as c', 'c.id', '=', 'pp.color_id')
            ->join('warehouses as w', 'w.id', '=', 'pp.warehouse_id')
            ->join('agents as a', 'a.id', '=', 'pp.agent_id')
            ->join('product_images as pi', 'pi.product_id', '=', 'pp.product_id')
            ->where('pp.warehouse_id', $id)
            ->groupBy('pp.product_id', 'pp.brand_id', 'pp.size_id', 'pp.color_id', 'pp.agent_id')
            ->select('p.id', 'p.product_code', 'p.name', 'pi.image', 'b.name as brand_name', 's.name as size_name', 'c.code_color', 'a.name as agent_name', 'pp.quantity', 'pp.expired_date')
            ->get();
        return response()->json($results);
    }

    public function warehouseFilter(Request $request){
        $request->validate([
            'status' => 'string',
            'from' => 'date',
            'to' => 'date'
        ]);
        $query = DB::table('warehouses');

        if ($request->status != null) {
            $query->where('status', $request->status);
        }
    
        if ($request->from != null && $request->to != null) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        } elseif ($request->from != null) {
            $query->whereDate('created_at', $request->from);
        } elseif ($request->to != null) {
            $query->whereDate('created_at', $request->to);
        }
    
        $results = $query->paginate(5);
    
        return response()->json($results);
    }

    public function deleteWarehouse($id)
    {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            return response()->json([
                'message' => 'Warehouse does not exist'
            ], 404);
        }

        if ($warehouse->delete()) {
            return response()->json([
                'message' => 'Warehouse deleted successfully'
            ]);
        }

        return response()->json([
            'message' => 'Something went wrong'
        ]);
    }
}
