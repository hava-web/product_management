<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function addWarehouse(Request $request){
        $request->validate([
            'name' => 'required|string',
            'manager'=>'required|int',
            'city'=>'required|string',
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

        if($warehouse->save()){
            return response()->json([$warehouse]);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }
}
