<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'images' => 'nullable',
            'name' => 'required|string',
            'category' => 'required|integer',
            'description' => 'required|string',
            'brand' => 'required|string',
            'original_price' => 'required|integer',
            'selling_price' => 'required|integer',
            'status' => 'required|string',
            'quantity' => 'required|int',
            'imported_date' => 'required|date',
            'expired_date' => 'required|date',
            'warehouse_id' => 'required|integer',
            'delivered_from' => 'required|string',
            'sale_percentage' => 'required|int',
            'colors' => 'nullable'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->description = $request->description;
        $product->brand = $request->brand;
        $product->status = $request->status;
        $product->original_price = $request->original_price;
        $product->selling_price = $request->selling_price;
        $product->quantity = $request->quantity;
        $product->imported_date = $request->imported_date;
        $product->expired_date = $request->expired_date;
        $product->warehouse_id = $request->warehouse_id;
        $product->delivered_from = $request->delivered_from;
        $product->sale_percentage = $request->sale_percentage;

        if ($request->colors && $product->save()) {
            $sum = 0;
            foreach ($request->colors as $color) {
                $sum += intval($color['quantity']);
            }

            if ($sum > $request->quantity) {
                return response()->json([
                    'message' => 'Sum of color are larger than product quantity'
                ],422);
            } else {
                foreach ($request->colors as $color) {
                    $product->productColors()->create([
                        'product_id' => $product->id,
                        'color_id' => intval($color['id']),
                        'quantity' => intval($color['quantity']),
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
}
