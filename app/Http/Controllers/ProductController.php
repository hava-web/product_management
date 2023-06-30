<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'images' => 'nullable',
            'name' => 'required|string',
            'category' => 'required|integer',
            'description' => 'required|string',
            'original_price' => 'required|integer',
            'properties' => 'required',
            'selling_price' => 'required|integer',
            'imported_date' => 'required|date',
            'delivered_from' => 'required|string',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->description = $request->description;
        $product->original_price = $request->original_price;
        $product->selling_price = $request->selling_price;
        $product->quantity = $request->quantity;
        $product->imported_date = $request->imported_date;
        $product->delivered_from = $request->delivered_from;

        if ($request->properties && $product->save()) {
            $sum = 0;
            foreach ($request->properties as $property) {
                $sum += intval($property['quantity']);
            }

            if ($sum > $request->quantity) {
                return response()->json([
                    'message' => 'Sum of color are larger than product quantity'
                ],422);
            } else {
                foreach ($request->properties as $property) {
                    $product->productProperty()->create([
                        'product_id' => $product->id,
                        'color_id' => intval($property['color']),
                        'quantity' => intval($property['quantity']),
                        'sale_percentage' => intval($property['discount']),
                        'expired_date' => $property['expired_date'],
                        'status' => $property['status'],
                        'warehouse_id' => intval($property['warehouse_id']),
                        'brand_id' => intval($property['brand']),
                        'size_id' => intval($property['size']),
                    ]);
                }
            }
        }

        if ($request->hasFile('images') && $product->save()) {
            $i = 1;
            foreach ($request->images as $imageFile) {
                $ext = $imageFile->extension();
                $file = time() . $i++ . '.' . $ext;
                $imageFile->storeAs('images', $file, 'public');
                $product->productImages()->create([
                    'product_id' => $product->id,
                    'image' => $file
                ]);
            }
        }

        if ($product->save()) {
            return response()->json([
                'message' => 'Product Added Successfully',
            ], 201);
        } else {
            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function getByPage(){
        $products = Product::paginate(3);
        return response()->json($products);
    }

    public function getImage($id){
        $image = ProductImage::where('product_id', $id)->first();
        if($image){
            return response()->json($image);
        }
        else{
            return response()->json([
                'message' => 'Product does not exits'
            ]);
        }
    }

    public function getAllImageById($id){
        $images = ProductImage::where('product_id', $id)->get();
        if($images){
            return response()->json($images);
        }
        else{
            return response()->json([
                'message' => 'Product does not exits'
            ]);
        }
    }
    
    public function getProperties($id , $warehouse_id){
        $properties = DB::table('product_properties as pp')
        ->join('colors as c', 'c.id', '=', 'pp.color_id')
        ->join('sizes as s', 's.id', '=', 'pp.size_id')
        ->join('brands as b', 'b.id', '=', 'pp.brand_id')
        ->select('s.name as size_name', 'b.name as brand_name', 'c.name as color_name', 'c.code_color as color_code', 'pp.quantity', 'pp.expired_date', 'pp.status')
        ->where('product_id', $id)
        ->where('warehouse_id', $warehouse_id)
        ->get();
        if($properties){
            return response()->json($properties);
        }
        else{
            return response()->json([
                'message' => 'Product or warehouse does not exits'
            ]);
        }
    }

    public function getWarehouse($id){
        $warehouse = ProductProperty::where('product_id', $id)->distinct('warehouse_id')->pluck('warehouse_id');

        if($warehouse){
            return response()->json($warehouse);
        }
        else{
            return response()->json([
                'message' => 'product does not exits'
            ]);
        }

    }

    public function getById($id){
        $product = Product::find($id);
        if($product){
            return response()->json($product);
        }
        else{
            return response()->json([
                'message' => 'Product does not exits'
            ],404);
        }
    }

    public function destroy($id){
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json([
                'message' => 'Product does not exist'
            ], 404);
        }
    
        $properties = ProductProperty::where('product_id', $product->id)->get();
        $images = ProductImage::where('product_id', $product->id)->get();
    
        foreach($properties as $property) {
            $property->delete();
        }
    
        foreach($images as $image) {
            $image->delete();
        }
    
        $product->delete();
    
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }


}
