<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|int',
        ]);

       $size = new Size([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        if ($size->save()) {
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
        $sizes = Size::where('status',1)->get();
        return response()->json($sizes);
    }


    public function getByPage()
    {
        $sizes = Size::paginate(3);
        return response()->json($sizes);
    }

    public function getById($id)
    {
        $size = Size::find($id);
        if ($size) {
            return response()->json($size);
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

        $size = Size::find($id);
        if($size){
            $size->name = $validatedData['name'];
            $size->status = $validatedData['status'];

            if($size->save()){
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
        $size = Size::find($id);
        if (!$size) {
            return response()->json([
                'message' => 'Brand does not exist'
            ], 404);
        }
    
        if ($size->delete()) {
            return response()->json([
                'message' => 'Brand deleted successfully'
            ]);
        }
    
        return response()->json([
            'message' => 'Something went wrong'
        ]);
    }
}
