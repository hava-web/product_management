<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'product_code' => 'required|string|unique:products',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->imported_date = $request->imported_date;
        $product->product_code = $request->product_code;

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
                        'agent_id' => intval($property['agent']),
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
            ->join('agents as a', 'a.id', '=', 'pp.agent_id')
            ->select('s.name as size_name', 'b.name as brand_name', 'c.name as color_name', 'c.code_color as color_code', 'a.name as agent', 'pp.quantity', 'pp.expired_date', 'pp.status', 'pp.original_price', 'pp.selling_price')
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

    public function getWarehouseByProperties($id, $brand_id, $size_id, $color_id)
    {
        $warehouses = DB::table('product_properties AS pp')
            ->join('brands AS b', 'pp.brand_id', '=', 'b.id')
            ->join('sizes AS s', 'pp.size_id', '=', 's.id')
            ->join('colors AS c', 'pp.color_id', '=', 'c.id')
            ->join('warehouses AS w', 'pp.warehouse_id', '=', 'w.id')
            ->select('w.id', 'w.name')
            ->where('pp.product_id', $id)
            ->where('pp.brand_id', $brand_id)
            ->where('pp.size_id', $size_id)
            ->where('pp.color_id', $color_id)
            ->get();
        if ($warehouses->isNotEmpty()) {
            return response()->json($warehouses);
        } else {
            return response()->json([
                'message' => 'colors does not exits'
            ], 404);
        }
    }

    public function getAgentByProperties($id, $brand_id, $size_id, $color_id, $warehouse_id)
    {
        $agents = DB::table('product_properties AS pp')
            ->join('agents AS a', 'pp.agent_id', '=', 'a.id')
            ->select('a.id', 'a.name')
            ->where('pp.product_id', '=', $id)
            ->where('pp.brand_id', '=', $brand_id)
            ->where('pp.size_id', '=', $size_id)
            ->where('pp.color_id', '=', $color_id)
            ->where('pp.warehouse_id', '=', $warehouse_id)
            ->get();
        if ($agents->isNotEmpty()) {
            return response()->json($agents);
        } else {
            return response()->json([
                'message' => 'colors does not exits'
            ], 404);
        }
    }

    public function getDiscount($id, $brand_id, $size_id, $color_id, $warehouse_id, $agent_id)
    {
        $discount = ProductProperty::select('sale_percentage', 'original_price', 'selling_price', 'quantity')
            ->where('product_id', $id)
            ->where('brand_id', $brand_id)
            ->where('size_id', $size_id)
            ->where('color_id', $color_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('agent_id', $agent_id)
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
        $product = Product::find($id);
        $validatedData = $request->validate([
            'images' => 'nullable',
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'category' => 'required|integer',
            'description' => 'required|string',
            'properties' => 'required',
            'imported_date' => 'required|date',
            'product_code' => 'required|string|unique:products,product_code,' . $product->id,
            // 'properties.*.warehouse_id' => 'required|integer',
        ]);
        if ($product) {
            $product->name = $validatedData['name'];
            $product->category = $validatedData['category'];
            $product->quantity = $validatedData['quantity'];
            $product->description = $validatedData['description'];
            $product->imported_date = $validatedData['imported_date'];
            $product->product_code = $validatedData['product_code'];
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
                        'agent_id' => intval($property['agent_id']),
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

    public function totalProduct()
    {
        $totalProducts = Product::count('id');
        return response()->json($totalProducts);
    }

    public function inventories()
    {
        $products = ProductProperty::select(
            'products.id AS id',
            'products.name AS product',
            'brands.name AS brand',
            'sizes.name AS size',
            'colors.code_color AS colors',
            'product_properties.quantity',
            DB::raw('DATEDIFF(product_properties.expired_date, CURDATE()) AS day_diff')
        )
            ->join('products', 'products.id', '=', 'product_properties.product_id')
            ->join('brands', 'brands.id', '=', 'product_properties.brand_id')
            ->join('sizes', 'sizes.id', '=', 'product_properties.size_id')
            ->join('colors', 'colors.id', '=', 'product_properties.color_id')
            ->whereRaw('DATEDIFF(product_properties.expired_date, CURDATE()) < 2000')
            ->paginate(5);

        return response()->json($products);
    }

    public function productBuyMost()
    {
        $products = OrderDetail::selectRaw('CONCAT(products.name, "-", brands.name, "-", sizes.name, "-", colors.name) AS x')
            ->selectRaw('SUM(order_details.quantity) AS y')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->join('brands', 'brands.id', '=', 'order_details.brand_id')
            ->join('sizes', 'sizes.id', '=', 'order_details.size_id')
            ->join('colors', 'colors.id', '=', 'order_details.color_id')
            ->groupBy('order_details.product_id', 'order_details.brand_id', 'order_details.size_id', 'order_details.color_id')
            ->orderByDesc(DB::raw('SUM(order_details.quantity)'))
            ->limit(4)
            ->get();
        return response()->json($products);
    }

    public function productBuyMostInterval(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);

        $results = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->join('brands as b', 'b.id', '=', 'od.brand_id')
            ->join('sizes as s', 's.id', '=', 'od.size_id')
            ->join('colors as c', 'c.id', '=', 'od.color_id')
            ->select(DB::raw('CONCAT(p.name, "-", b.name, "-", s.name, "-", c.name) AS x'), DB::raw('SUM(od.quantity) AS y'))
            ->whereBetween('od.created_at', [$request->from, $request->to])
            ->groupBy('od.product_id', 'od.brand_id', 'od.size_id', 'od.color_id')
            ->orderByRaw('SUM(od.quantity)')
            ->limit(4)
            ->get();
        return response()->json($results);
    }

    public function productByDate()
    {
        $product = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->select(DB::raw('DATE(od.created_at) AS x'), DB::raw('CAST(SUM(od.quantity) AS UNSIGNED) AS y'))
            ->where('o.status', '=', 'Received')
            ->groupBy(DB::raw('DATE(od.created_at)'))
            ->get();

        return response()->json($product);
    }

    public function productByDateInterval(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);

        $results =  DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->select(DB::raw('DATE(od.created_at) AS x'), DB::raw('CAST(SUM(od.quantity) AS UNSIGNED) AS y'))
            ->where('o.status', '=', 'Received')
            ->whereBetween('od.created_at', [$request->from, $request->to])
            ->groupBy(DB::raw('DATE(od.created_at)'))
            ->get();

        if ($results) {
            return response()->json($results);
        } else {
            return response()->json([
                'message' => 'Somthing went wrong'
            ], 500);
        }
    }

    public function allProductsProp()
    {
        $result = DB::table('product_properties')
            ->select('products.id', 'products.name as product', 'products.product_code', 'product_images.image', 'brands.name as brand_name', 'sizes.name as size_name', 'colors.name as color_name', 'colors.code_color', 'product_properties.quantity')
            ->join('products', 'products.id', '=', 'product_properties.product_id')
            ->join('brands', 'brands.id', '=', 'product_properties.brand_id')
            ->join('sizes', 'sizes.id', '=', 'product_properties.size_id')
            ->join('colors', 'colors.id', '=', 'product_properties.color_id')
            ->join('product_images', 'product_images.product_id', '=', 'product_properties.product_id')
            ->groupBy('products.id', 'product_properties.brand_id', 'product_properties.size_id', 'product_properties.color_id', 'product_properties.agent_id', 'product_properties.warehouse_id')
            ->get();

        return response()->json($result);
    }

    public function filterProduct(Request $request)
    {
        $request->validate([
            'from' => 'date|nullable',
            'to' => 'date|nullable',
            'category' => 'int|nullable',
            'brand' => 'int|nullable',
            'size' => 'int|nullable',
            'agent' => 'int|nullable',
            'warehouse' => 'int|nullable',
        ]);
        $query = DB::table('product_properties as pp')
            ->select('p.id', 'p.name as product', 'p.product_code', 'pi.image', 'b.name as brand_name', 's.name as size_name', 'c.name as color_name', 'c.code_color', 'pp.quantity')
            ->join('products as p', 'p.id', '=', 'pp.product_id')
            ->join('brands as b', 'b.id', '=', 'pp.brand_id')
            ->join('sizes as s', 's.id', '=', 'pp.size_id')
            ->join('colors as c', 'c.id', '=', 'pp.color_id')
            ->join('product_images as pi', 'pi.id', '=', 'pp.product_id')
            ->join('categories as ca', 'ca.id', '=', 'p.category')
            ->groupBy('p.id', 'pp.brand_id', 'pp.size_id', 'pp.color_id', 'pp.agent_id', 'pp.warehouse_id');

        if ($request->category != null) {
            $query->where('p.category', '=', $request->category);
        }

        if ($request->brand != null) {
            $query->where('pp.brand_id', '=', $request->brand);
        }

        if ($request->size != null) {
            $query->where('pp.size_id', '=', $request->size);
        }

        if ($request->color != null) {
            $query->where('pp.color_id', '=', $request->color);
        }

        if ($request->warehouse != null) {
            $query->where('pp.warehouse_id', '=', $request->warehouse);
        }

        if ($request->agent != null) {
            $query->where('pp.agent_id', '=', $request->agent);
        }

        if ($request->from != null && $request->to != null) {
            $query->whereBetween('pp.created_at', [$request->from, $request->to]);
        } else {
            if ($request->from != null) {
                $query->whereDate('pp.created_at', '=', $request->from);
            }

            if ($request->to != null) {
                $query->whereDate('pp.created_at', '=', $request->to);
            }
        }

        $products = $query->get();

        return response()->json($products);
    }

    public function filterProductByPage(Request $request)
    {
        $request->validate([
            'from' => 'date|nullable',
            'to' => 'date|nullable',
            'category' => 'int|nullable',
            'brand' => 'int|nullable',
            'size' => 'int|nullable',
            'agent' => 'int|nullable',
            'warehouse' => 'int|nullable',
        ]);
        $query = DB::table('product_properties as pp')
            ->select('p.id', 'p.name as name', 'p.product_code', 'p.imported_date', 'pi.image', 'b.name as brand_name', 's.name as size_name', 'c.name as color_name', 'c.code_color', 'pp.quantity')
            ->join('products as p', 'p.id', '=', 'pp.product_id')
            ->join('brands as b', 'b.id', '=', 'pp.brand_id')
            ->join('sizes as s', 's.id', '=', 'pp.size_id')
            ->join('colors as c', 'c.id', '=', 'pp.color_id')
            ->join('product_images as pi', 'pi.id', '=', 'pp.product_id')
            ->join('categories as ca', 'ca.id', '=', 'p.category')
            ->groupBy('p.id', 'pp.brand_id', 'pp.size_id', 'pp.color_id', 'pp.agent_id', 'pp.warehouse_id');

        if ($request->category != null) {
            $query->where('p.category', '=', $request->category);
        }

        if ($request->brand != null) {
            $query->where('pp.brand_id', '=', $request->brand);
        }

        if ($request->size != null) {
            $query->where('pp.size_id', '=', $request->size);
        }

        if ($request->color != null) {
            $query->where('pp.color_id', '=', $request->color);
        }

        if ($request->warehouse != null) {
            $query->where('pp.warehouse_id', '=', $request->warehouse);
        }

        if ($request->agent != null) {
            $query->where('pp.agent_id', '=', $request->agent);
        }

        if ($request->from != null && $request->to != null) {
            $query->whereBetween('pp.created_at', [$request->from, $request->to]);
        } else {
            if ($request->from != null) {
                $query->whereDate('pp.created_at', '=', $request->from);
            }

            if ($request->to != null) {
                $query->whereDate('pp.created_at', '=', $request->to);
            }
        }

        $products = $query->paginate(5);

        return response()->json($products);
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
