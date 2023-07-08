<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class CustomerController extends Controller
{
    public function all()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    public function getById($id)
    {
        $customer = Customer::find($id);
        if ($id) {
            return response()->json($customer);
        } else {
            return response()->json([
                'message' => 'Customer does not exit'
            ], 404);
        }
    }

    public function totalCus()
    {
        $totalCustomers = Customer::count('id');
        return response()->json($totalCustomers);
    }

    public function getByPage()
    {
        $customers = Customer::paginate(10);
        return response()->json($customers);
    }

    public function getOrders($customer_id)
    {
        $orders = DB::table('orders AS o')
            ->select('o.id', 'o.total_price', 'o.status', 'o.payment_mode')
            ->where('o.customer_id', $customer_id)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found for the customer'
            ]);
        } else {
            return response()->json($orders);
        }
    }

    public function customers()
    {
        $customers = DB::table('customers AS c')
            ->select('c.id', 'c.lastname', 'c.firstname', 'c.numbers_of_purchases')
            ->distinct()
            ->get();

        return response()->json($customers);
    }
}
