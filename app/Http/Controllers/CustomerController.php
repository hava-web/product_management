<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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
            ->select(DB::raw('CONCAT(c.lastname, " ", c.firstname) AS x'), 'c.numbers_of_purchases AS y')
            ->orderByDesc('c.numbers_of_purchases')
            ->take(5)
            ->get();

        return response()->json($customers);
    }

    public function customerByMostByInterval(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $results = DB::table('customers as c')
            ->select(DB::raw('CONCAT(c.lastname, " ", c.firstname) AS x'), 'c.numbers_of_purchases AS y')
            ->whereBetween('c.updated_at', [$request->from, $request->to])
            ->orderByDesc('c.numbers_of_purchases')
            ->limit(5)
            ->get();
        return response()->json($results);
    }

    public function customerByWeek()
    {
        $results = DB::table('customers as c')
            ->select(DB::raw("CONCAT('Week ', WEEK(c.created_at)) AS x"), DB::raw('COUNT(*) AS y'))
            ->whereYear('c.created_at', '=', date('Y'))
            ->groupBy('x')
            ->get();
        return response()->json($results);
    }

    public function customerByMounth()
    {
        $customers = DB::table('customers as c')
            ->select(DB::raw("MONTHNAME(c.created_at) AS x"), DB::raw('COUNT(*) AS y'))
            ->whereYear('c.created_at', '=', date('Y'))
            ->groupBy(DB::raw('DATE(c.created_at)'))
            ->get();

        return response()->json($customers);
    }

    public function customerByYear()
    {
        $results = DB::table('customers as c')
            ->select(DB::raw('YEAR(c.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->groupBy('x')
            ->get();

        return response()->json($results);
    }

    public function customerByDate()
    {
        $results = DB::table('customers as c')
            ->select(DB::raw('DATE(c.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->groupBy('x')
            ->get();
        return response()->json($results);
    }

    public function cusNumberByInterval(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $results = DB::table('customers as c')
            ->select(DB::raw('DATE(c.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->whereBetween(DB::raw('DATE(c.created_at)'), [$request->from, $request->to])
            ->groupBy('x')
            ->get();
        return response()->json($results);
    }
}
