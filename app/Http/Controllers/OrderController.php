<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|string',
            'products' => 'required',
            'email' => 'required|string|email',
            'address' => 'required|string',
            'payment_mode' => 'required|string',
        ]);

        if (Customer::where('id', $request->id)->exists()) {
            Customer::where('id', $request->id)->increment('numbers_of_purchases', 1);
            if ($request->products) {
                $order = new Order();
                $order->customer_id = $request->id;
                $order->total_price = $request->total_price;
                $order->user_id = auth()->id();
                $order->status = 'Ordered';
                $order->payment_mode = $request->payment_mode;

                if ($order->save()) {
                    foreach ($request->products as $product) {
                        $productGet = Product::find(intval($product['product']));
                        $productProp = ProductProperty::where('product_id', intval($product['product']))
                            ->where('brand_id', intval($product['brand']))
                            ->where('size_id', intval($product['size']))
                            ->where('color_id', intval($product['color']))
                            ->first();

                        if (($productGet->quantity == 0) || ($productProp->quantity == 0)) {
                            return response()->json([
                                'message' => 'product is out of stock'
                            ]);
                        } else {
                            $productGet->decrement('quantity', intval($product['quantity']));
                            $productProp->decrement('quantity', intval($product['quantity']));
                        }

                        $orderDetail = OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => intval($product['product']),
                            'agent_id' => intval($product['agent']),
                            'warehouse_id' => intval($product['warehouse']),
                            'brand_id' => intval($product['brand']),
                            'size_id' => intval($product['size']),
                            'color_id' => intval($product['color']),
                            'quantity' => intval($product['quantity']),
                            'discount' => intval($product['discount']),
                            'price' => intval($product['price']),
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Order not be saved'
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Product or customer went wrong'
                ], 500);
            }

            if ($order->save()) {
                return response()->json([
                    'message' => 'order added successfully'
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ], 500);
            }
        } else {
            $customer = new Customer();
            $customer->firstname = $request->firstname;
            $customer->lastname = $request->lastname;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->numbers_of_purchases = 1;

            if ($request->products && $customer->save()) {
                $order = new Order();
                $order->customer_id = $customer->id;
                $order->total_price = $request->total_price;
                $order->user_id = auth()->id();
                $order->status = 'Ordered';
                $order->payment_mode = $request->payment_mode;

                if ($order->save()) {
                    foreach ($request->products as $product) {
                        $productGet = Product::find(intval($product['product']));
                        $productProp = ProductProperty::where('product_id', intval($product['product']))
                            ->where('brand_id', intval($product['brand']))
                            ->where('size_id', intval($product['size']))
                            ->where('color_id', intval($product['color']))
                            ->first();
                        if (($productGet->quantity == 0) || ($productProp->quantity == 0)) {
                            return response()->json([
                                'message' => 'product is out of stock'
                            ]);
                        } else {
                            $productGet->decrement('quantity', intval($product['quantity']));
                            $productProp->decrement('quantity', intval($product['quantity']));
                        }

                        $orderDetail = OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => intval($product['product']),
                            'agent_id' => intval($product['agent']),
                            'warehouse_id' => intval($product['warehouse']),
                            'brand_id' => intval($product['brand']),
                            'size_id' => intval($product['size']),
                            'color_id' => intval($product['color']),
                            'quantity' => intval($product['quantity']),
                            'discount' => intval($product['discount']),
                            'price' => intval($product['price']),
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Order not be saved'
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Product or customer went wrong'
                ], 500);
            }

            if ($customer->save() && $order->save()) {
                return response()->json([
                    'message' => 'order added successfully'
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ], 500);
            }
        }
    }

    public function getByPage()
    {
        $orders = Order::paginate(3);
        return response()->json($orders);
    }

    public function getById($id)
    {
        $order = Order::find($id);
        if ($order) {
            return response()->json($order);
        } else {
            return response()->json([
                'message' => 'order does not exits'
            ]);
        }
    }

    public function getCustomer($order_id)
    {
        $customer = DB::table('orders AS o')
            ->join('customers AS c', 'c.id', '=', 'o.customer_id')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select('c.id', 'c.firstname', 'c.lastname', 'c.phone', 'c.email', 'c.address', 'e.id as employee_id' , 'e.firstname as emp_firstname', 'e.lastname as emp_lastname')
            ->where('o.id', $order_id)
            ->first();

        if ($customer) {
            return response()->json($customer);
        } else {
            return response()->json([
                'message' => 'Order does not exist'
            ], 404);
        }
    }

    public function getProducts($order_id)
    {
        $products = DB::table('orders AS o')
            ->join('order_details AS od', 'od.order_id', '=', 'o.id')
            ->join('products AS p', 'od.product_id', '=', 'p.id')
            ->join('brands AS b', 'od.brand_id', '=', 'b.id')
            ->join('sizes AS s', 'od.size_id', '=', 's.id')
            ->join('colors AS c', 'od.color_id', '=', 'c.id')
            ->select('od.order_id', 'p.id', 'p.name AS product_name', 'b.name AS brand_name', 's.name AS size_name', 'c.name AS color_name', 'c.code_color AS color_code', 'od.quantity', 'od.discount', 'od.price')
            ->where('o.id', $order_id)
            ->get();
        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Order does not exist'
            ]);
        } else {
            return response()->json($products);
        }
    }

    public function totalOrder()
    {
        $totalOrders = Order::count('id');
        return response()->json($totalOrders);
    }

    public function revenue()
    {
        $totalPrice = Order::where('status', 'Received')->sum('total_price');
        return $totalPrice;
    }

    public function getReceivedOrder()
    {
        $orders = Order::where('status', 'Received')->paginate(5);
        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found '
            ]);
        } else {
            return response()->json($orders);
        }
    }

    public function orderDate()
    {
        $results = DB::table('orders')
            ->select(DB::raw('DATE(created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->where('status', 'Received')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        return response()->json($results);
    }

    public function orderByWeek()
    {
        $results = DB::table('orders')
            ->select(DB::raw("CONCAT('Week ', WEEK(created_at)) AS x"), DB::raw('COUNT(*) AS y'))
            ->where('status', 'Received')
            ->whereYear('created_at', '=', date('Y'))
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->get();
        return response()->json($results);
    }

    public function orderByMonth()
    {
        $result = DB::table('orders')
            ->select(DB::raw('MONTHNAME(created_at) as x'), DB::raw('COUNT(*) as y'))
            ->where('status', 'Received')
            ->whereYear('created_at', '=', date('Y'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
        return response()->json($result);
    }

    public function orderByYear()
    {
        $result = DB::table('orders')
            ->select(DB::raw('YEAR(created_at) as x'), DB::raw('COUNT(*) as y'))
            ->where('status', 'Received')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->get();
        return response()->json($result);
    }

    public function orderDateInterval(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $result = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as x'), DB::raw('COUNT(*) as y'))
            ->where('status', '=', 'Received')
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->from, $request->to])
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->get();

        return response()->json($result);
    }

    public function orderFilter(Request $request)
    {
        $request->validate([
            'status' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $query = DB::table('orders');

        if ($request->status != null) {
            $query->where('status', $request->status);
        }

        if ($request->from != null && $request->to != null) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        } elseif ($request->from != null) {
            $query->whereDate('created_at', $request->from);
        } elseif ($request->to != null) {
            $query->whereDate('created_at', $request->to);
        }

        $results = $query->paginate(5);

        return response()->json($results);
    }

    public function revenueInterval(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $result = DB::table('orders AS o')
            ->select(DB::raw('SUM(o.total_price) AS revenue'))
            ->where('o.status', 'Received')
            ->whereBetween(DB::raw('date(o.created_at)'), [$request->from, $request->to])
            ->first();
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|string',
        ]);
        $order = Order::find($id);
        if ($order) {
            $order->status = $validatedData['status'];

            if ($order->save()) {
                return response()->json([
                    'message' => 'Order Updated Successfully'
                ]);
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Order does not exits'
            ], 404);
        }
    }
}
