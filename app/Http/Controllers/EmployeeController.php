<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $employees = Employee::paginate(3);
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
            ->select('e.firstname', 'e.lastname', 'e.image', 'u.name', 'u.email', 'e.phone', 'e.date_of_birth', 'e.city', 'e.address')
            ->where('u.id', '=', $id)
            ->first();

        return response()->json($employee);
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
}
