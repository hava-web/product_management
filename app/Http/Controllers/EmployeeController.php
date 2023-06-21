<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function getEmployeeById( $id ){
        $employee = Employee::where('id', $id)->first();
        if($employee){
            return response()->json($employee);
        }else{
            return response()->json([
                'message' => 'Employee does not exits'
            ]);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id ){
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee does not exist'
            ], 404);
        }else{
            $user = User::find($employee->user_id);
            if($employee->delete() && $user->delete()){
                return response()->json([
                    'message' => 'Employee Deleted SuccessFully'
                ]);
            }
        }
    
        
    
        return response()->json([
            'message' => 'Something went wrong'
        ],500);
    }
}
