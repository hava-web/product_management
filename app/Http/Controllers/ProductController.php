<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'images' => 'nullable',
            'name' => 'required|string',
            'category' => 'required|integer',
            'properties' => 'required',
            'quantity' => 'required|integer',
            'imported_date' => 'required|date',
            'delivered_from' => 'required|string',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->description = $request->description;
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
                ], 422);
            } else {
                foreach ($request->properties as $property) {
                    $product->productProperty()->create([
                        'product_id' => $product->id,
                        'color_id' => intval($property['color']),
                        'quantity' => intval($property['quantity']),
                        'sale_percentage' => intval($property['discount']),
                        'expired_date' => $property['expired_date'],
                        'original_price' => intval($property['original_price']),
                        'selling_price' => intval($property['selling_price']),
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

    public function getByPage()
    {
        $products = Product::paginate(3);
        return response()->json($products);
    }

    public function getImage($id)
    {
        $image = ProductImage::where('product_id', $id)->first();
        if ($image) {
            return response()->json($image);
        } else {
            return response()->json([
                'message' => 'Product does not exits'
            ]);
        }
    }

    public function getAllImageById($id)
    {
        $images = ProductImage::where('product_id', $id)->get();
        if ($images) {
            return response()->json($images);
        } else {
            return response()->json([
                'message' => 'Product does not exits'
            ]);
        }
    }

    public function getPropertiesAndWare($id, $warehouse_id)
    {
        $properties = DB::table('product_properties as pp')
            ->join('colors as c', 'c.id', '=', 'pp.color_id')
            ->join('sizes as s', 's.id', '=', 'pp.size_id')
            ->join('brands as b', 'b.id', '=', 'pp.brand_id')
            ->select('s.name as size_name', 'b.name as brand_name', 'c.name as color_name', 'c.code_color as color_code', 'pp.quantity', 'pp.expired_date', 'pp.status', 'pp.original_price', 'pp.selling_price')
            ->where('product_id', $id)
            ->where('warehouse_id', $warehouse_id)
            ->get();
        if ($properties) {
            return response()->json($properties);
        } else {
            return response()->json([
                'message' => 'Product or warehouse does not exits'
            ]);
        }
    }

    public function getProperties($id)
    {
        $properties = ProductProperty::where('product_id', $id)->get();
        if ($properties) {
            return response()->json($properties);
        } else {
            return response()->json([
                'message' => 'properties does not exits'
            ]);
        }
    }

    public function getWarehouse($id)
    {
        $warehouse = ProductProperty::where('product_id', $id)->distinct('warehouse_id')->pluck('warehouse_id');

        if ($warehouse) {
            return response()->json($warehouse);
        } else {
            return response()->json([
                'message' => 'product does not exits'
            ]);
        }
    }

    public function getBrandByProId($id)
    {
        $brands = DB::table('product_properties as pp')
            ->join('brands as b', 'b.id', '=', 'pp.brand_id')
            ->select('pp.brand_id as id', 'b.name as name')
            ->where('pp.product_id', $id)
            ->distinct()
            ->get();
        if ($brands->isNotEmpty()) {
            return response()->json($brands);
        } else {
            return response()->json([
                'message' => 'brands not found'
            ], 404);
        }
    }

    public function getSizeByBrandPro($id, $brand_id)
    {
        $sizes = DB::table('product_properties')
            ->join('brands', 'brands.id', '=', 'product_properties.brand_id')
            ->join('sizes', 'sizes.id', '=', 'product_properties.size_id')
            ->select('sizes.id AS id', 'sizes.name AS name')
            ->distinct()
            ->where('product_properties.product_id', $id)
            ->where('product_properties.brand_id', $brand_id)
            ->get();
        if ($sizes->isNotEmpty()) {
            return response()->json($sizes);
        } else {
            return response()->json([
                'message' => 'sizes does not exits'
            ], 404);
        }
    }

    public function getColorByBraProSiz($id, $brand_id, $size_id)
    {
        $colors = DB::table('product_properties')
            ->join('brands', 'brands.id', '=', 'product_properties.brand_id')
            ->join('sizes', 'sizes.id', '=', 'product_properties.size_id')
            ->join('colors', 'colors.id', '=', 'product_properties.color_id')
            ->select('colors.id AS id', 'colors.name AS name', 'colors.code_color AS code_color')
            ->distinct()
            ->where('product_properties.product_id', $id)
            ->where('product_properties.brand_id', $brand_id)
            ->where('product_properties.size_id', $size_id)
            ->get();
        if ($colors->isNotEmpty()) {
            return response()->json($colors);
        } else {
            return response()->json([
                'message' => 'colors does not exits'
            ], 404);
        }
    }

    public function getDiscount($id, $brand_id, $size_id, $color_id)
    {
        $discount = DB::table('product_properties AS pp')
            ->join('brands AS b', 'b.id', '=', 'pp.brand_id')
            ->join('sizes AS s', 's.id', '=', 'pp.size_id')
            ->join('colors AS c', 'c.id', '=', 'pp.color_id')
            ->select('pp.sale_percentage', 'pp.original_price', 'pp.selling_price', 'pp.quantity')
            ->where('pp.product_id', $id)
            ->where('pp.brand_id', $brand_id)
            ->where('pp.size_id', $size_id)
            ->where('pp.color_id', $color_id)
            ->first();

        if ($discount) {
            return response()->json($discount);
        } else {
            return response()->json([
                'message' => 'Sale percentage does not exist'
            ]);
        }
    }

    public function all()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function getById($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json($product);
        } else {
            return response()->json([
                'message' => 'Product does not exits'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'images' => 'nullable',
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'category' => 'required|integer',
            'description' => 'required|string',
            'properties' => 'required',
            'imported_date' => 'required|date',
            'delivered_from' => 'required|string',
            // 'properties.*.warehouse_id' => 'required|integer',
        ]);
        $product = Product::find($id);
        if ($product) {
            $product->name = $validatedData['name'];
            $product->category = $validatedData['category'];
            $product->quantity = $validatedData['quantity'];
            $product->description = $validatedData['description'];
            $product->imported_date = $validatedData['imported_date'];
            $product->delivered_from = $validatedData['delivered_from'];
        }

        if ($request->properties && $product->save()) {
            $properties = ProductProperty::where('product_id', $id)->get();
            foreach ($properties as $property) {
                $property->delete();
            }
            $sum = 0;
            foreach ($request->properties as $property) {
                $sum += intval($property['quantity']);
            }

            if ($sum > $request->quantity) {
                return response()->json([
                    'message' => 'Sum of color are larger than product quantity'
                ], 422);
            } else {
                foreach ($request->properties as $property) {
                    $product->productProperty()->create([
                        'product_id' => $product->id,
                        'color_id' => intval($property['color_id']),
                        'quantity' => intval($property['quantity']),
                        'sale_percentage' => intval($property['sale_percentage']),
                        'original_price' => intval($property['original_price']),
                        'selling_price' => intval($property['selling_price']),
                        'expired_date' => $property['expired_date'],
                        'status' => $property['status'],
                        'warehouse_id' => intval($property['warehouse_id']),
                        'brand_id' => intval($property['brand_id']),
                        'size_id' => intval($property['size_id']),
                    ]);
                }
            }
        }

        if ($request->hasFile('images') && $product->save()) {
            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                $image->delete();
            }
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

    public function totalProduct(){
        $totalProducts = Product::count('id');
        return response()->json($totalProducts);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product does not exist'
            ], 404);
        }

        $properties = ProductProperty::where('product_id', $product->id)->get();
        $images = ProductImage::where('product_id', $product->id)->get();

        foreach ($properties as $property) {
            $property->delete();
        }

        foreach ($images as $image) {
            $image->delete();
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
