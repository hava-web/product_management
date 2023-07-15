<?php

namespace App\Http\Controllers;

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
        $warehouses = Warehouse::paginate(3);
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
