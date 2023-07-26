<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function viewAllEmployee()
    {
        $employees = Employee::all();
        return $employees;
    }

    public function getEmployeeByPage()
    {
        $employees = Employee::paginate(5);
        return response()->json($employees);
    }

    public function getEmployeeById($id)
    {
        $employee = Employee::where('id', $id)->first();
        if ($employee) {
            return response()->json($employee);
        } else {
            return response()->json([
                'message' => 'Employee does not exits'
            ]);
        }
    }

    public function getEmployeeByUser($id)
    {
        $employee = DB::table('employees AS e')
            ->join('users AS u', 'e.user_id', '=', 'u.id')
            ->select('e.id','e.firstname', 'e.lastname', 'e.user_id', 'e.image', 'u.name', 'u.email', 'e.phone', 'e.date_of_birth', 'e.city', 'e.address')
            ->where('u.id', '=', $id)
            ->first();

        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'image' => 'string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'name' => 'required|string',
            'phone' => 'required|string',
            'date_of_birth' => 'required|date',
            'city' => 'required|string',
            'address' => 'required|string',
            // 'properties.*.warehouse_id' => 'required|integer',
        ]);
        $user = User::find($id);
        if ($user) {
            $user->name = $validatedData['name'];
        }

        $employee = Employee::where('user_id', $id)->first();
        if ($employee) {
            $employee->image = $validatedData['image'];
            $employee->firstname = $validatedData['firstname'];
            $employee->lastname = $validatedData['lastname'];
            $employee->phone = $validatedData['phone'];
            $employee->date_of_birth = $validatedData['date_of_birth'];
            $employee->city = $validatedData['city'];
            $employee->address = $validatedData['address'];
        }


        if ($user->save() && $employee->save()) {
            return response()->json([
                'message' => 'employee updated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'something went wrong'
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee does not exist'
            ], 404);
        } else {
            $user = User::find($employee->user_id);
            if ($employee->delete() && $user->delete()) {
                return response()->json([
                    'message' => 'Employee Deleted SuccessFully'
                ]);
            }
        }

        return response()->json([
            'message' => 'Something went wrong'
        ], 500);
    }

    public function ordersCreatedEmp($id)
    {
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select(DB::raw('date(o.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->where('e.id', $id)
            ->groupBy('x')
            ->get();
        return response()->json($results);
    }

    public function ordersWeek($id)
    {
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select(DB::raw("CONCAT('Week ', WEEK(o.created_at)) AS x"), DB::raw('COUNT(*) AS y'))
            ->where('e.id', $id)
            ->whereYear('o.created_at', now()->year)
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get();
        return response()->json($results);
    }

    public function ordersMonth($id)
    {
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select(DB::raw('MONTHNAME(o.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->where('e.id', $id)
            ->whereYear('o.created_at', now()->year)
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get();
        return response()->json($results);
    }

    public function ordersYear($id)
    {
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select(DB::raw('YEAR(o.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->where('e.id', $id)
            ->groupBy(DB::raw('YEAR(o.created_at)'))
            ->get();
        return response()->json($results);
    }

    public function ordersDateSuccess($id)
    {
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select(DB::raw('DATE(o.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->where('e.id', $id)
            ->where('o.status', 'Received')
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get();
        return response()->json($results);
    }

    public function ordersIntervalEmp(Request $request, $id)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->select(DB::raw('DATE(o.created_at) AS x'), DB::raw('COUNT(*) AS y'))
            ->where('e.id', $id)
            ->where('e.status', 'Received')
            ->whereBetween(DB::raw('DATE(o.created_at)'), [$request->from, $request->to])
            ->groupBy(DB::raw('DATE(o.created_at)'))
            ->get();
        return response()->json($results);
    }

    public function ordersListEmp($id)
    {
        $results = DB::table('orders AS o')
            ->join('employees AS e', 'e.user_id', '=', 'o.user_id')
            ->join('customers AS c', 'c.id', '=', 'o.customer_id')
            ->select('o.id', DB::raw("CONCAT(c.lastname, ' ', c.firstname) AS customer"), 'o.total_price', DB::raw("CONCAT(e.lastname, ' ', e.firstname) AS employee"), 'o.status', 'o.payment_mode')
            ->where('e.id', $id)
            ->where('o.status', 'Received')
            ->get();
        return response()->json($results);
    }

    public function getAllUserEmployee()
    {
        $employees = Employee::select('employees.id as employee_id', 'users.name as username', DB::raw('CONCAT(employees.lastname, " ", employees.firstname) AS full_name'), 'users.email', 'employees.date_of_birth', 'employees.salary')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->get();
        return response()->json($employees);
    }
}
