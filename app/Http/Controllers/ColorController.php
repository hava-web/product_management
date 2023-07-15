<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'name' => 'required|string',
            'code_color' => 'required|string',
        ]);

        $color = new Color([
            'name' => $request->name,
            'code_color' => $request->code_color,
        ]);
        if($color->save()){
            return response()->json([
                'message' => 'Color Added Successfully'
            ],201);
        }else{
            return response()->json([
                'message' => 'Something Went Wrong'
            ],500);
        }
    }

    public function getByPage(){
        $color = Color::paginate(5);
        return response()->json($color);
    }

    public function getColor($id){
        $color = Color::find($id);
        if($color){
            return response()->json($color);
        }else{
            return response()->json([
                'message' => 'Color does not exits'
            ],404);
        }
    }

    public function getAll(){
        $colors = Color::all();
        return response()->json($colors);
    }

    public function update(Request $request, $id){
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code_color' => 'required|string',
        ]);

        $color = Color::find($id);
        if($color){
            $color->name = $validatedData['name'];
            $color->code_color = $validatedData['code_color'];

            if($color->save()){
                return response()->json([
                    'message' => 'Color Update Successfully'
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
                'message' => 'Color does not exits'
            ]);
        }
    }

    public function destroy($id){
        $color = Color::find($id);
        if (!$color) {
            return response()->json([
                'message' => 'Color does not exist'
            ], 404);
        }
    
        if ($color->delete()) {
            return response()->json([
                'message' => 'Color deleted successfully'
            ]);
        }
    
        return response()->json([
            'message' => 'Something went wrong'
        ]);
    }
}
