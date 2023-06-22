<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'mimes:jpg,bmp,png',
            'status' => 'required|int',
        ]);

        $category = new Category();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->extension();
            $file = time() . '.' . $ext;
            $image->storeAs('images', $file, 'public');
            $category->image = $file;
        }
        $category->name = $request->name;
        $category->status = $request->status;

        if ($category->save()) {
            return response()->json([
                'message' => 'Category Added Successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Something Went Wrong'
            ], 500);
        }
    }

    public function getCategoryByPage()
    {
        $category = Category::paginate(3);
        return response()->json($category);
    }

    public function getCategoryById($id)
    {
        $category = Category::find($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'image' => 'mimes:jpg,bmp,png',
            'status' => 'required|int',
        ]);

        $category = Category::find($id);
        if ($category) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $ext = $image->extension();
                $file = time() . '.' . $ext;
                $image->storeAs('images', $file, 'public');
                $category->image = $file;
            }
            $category->name = $validatedData['name'];
            $category->status = $validatedData['status'];
            if($category->save()){
                return response()->json([
                    'message' => 'Category updated successfully'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Category does not exits'
            ], 404);
        }
    }

    public function remove( $id ){
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category does not exist'
            ], 404);
        }
    
        if ($category->delete()) {
            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        }
    
        return response()->json([
            'message' => 'Something went wrong'
        ]);
    }
}
