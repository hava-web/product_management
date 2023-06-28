<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|int',
        ]);

        $brand = new Brand([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        if ($brand->save()) {
            return response()->json([
                'message' => 'Brand Added Successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Something Went Wrong'
            ], 500);
        }
    }

    public function all(){
        $brands = Brand::where('status',1)->get();
        return response()->json($brands);
    }


    public function getByPage()
    {
        $brand = Brand::paginate(3);
        return response()->json($brand);
    }

    public function getById($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            return response()->json($brand);
        } else {
            return response()->json([
                'message' => 'Brand does not exits'
            ], 404);
        }
    }

    public function update(Request $request, $id){
        $validatedData = $request->validate([
            'name' => 'required|string',
            'status' => 'required|int',
        ]);

        $brand = Brand::find($id);
        if($brand){
            $brand->name = $validatedData['name'];
            $brand->status = $validatedData['status'];

            if($brand->save()){
                return response()->json([
                    'message' => 'Brand Update Successfully'
                ]);
            }
            else{
                return response()->json([
                    'message' => 'Something went wrong'
                ]);
            }
        }
        else{
            return response()->json([
                'message' => 'Brand does not exits'
            ]);
        }
    }

    public function destroy($id){
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'message' => 'Brand does not exist'
            ], 404);
        }
    
        if ($brand->delete()) {
            return response()->json([
                'message' => 'Brand deleted successfully'
            ]);
        }
    
        return response()->json([
            'message' => 'Something went wrong'
        ]);
    }
}
