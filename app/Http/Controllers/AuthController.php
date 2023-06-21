<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
    * @return [string] message
    */
    public function register(Request $request)
    {
        $request->validate([
            'image' => 'string|nullable',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'name' => 'required|string',
            'password'=>'required|string',
            'email'=>'required|string|unique:users',
            'warehouse_id' => 'required|int',
            'phone' => 'required|string',
            'date_of_birth' => 'required|date',
            'role' => 'required|string',
            'salary' => 'int',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = new User([
            'name'  => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        $user->save();

        $employee = new Employee([
            'image' => $request->image,
            'firstname' => $request->firstname,
            'user_id' => $user->id,
            'lastname' => $request->lastname,
            'warehouse_id' => $request->warehouse_id,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'salary' => $request->salary,
            'city' => $request->city,
            'address' => $request->address,
        ]);

        $employee->save();

        if($user->save() && $employee->save()){
            // $tokenResult = $user->createToken('Personal Access Token');
            // $token = $tokenResult->plainTextToken;

            // return response()->json([
            // 'message' => 'Successfully created user!',
            // 'accessToken'=> $token,
            // ],201);
            return response()->json([
                'account' => $user,
                'employee' => $employee
            ],201);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }

    /**
    * Login user and create token
    *
    * @param  [string] email
    * @param  [string] password
    * @param  [boolean] remember_me
    */

    public function login(Request $request)
    {
        $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
        ]);

        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials))
        {
        return response()->json([
            'message' => 'Unauthorized'
        ],401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
        'accessToken' =>$token,
        'token_type' => 'Bearer',
        ]);
    }

    /**
    * Get the authenticated User
    *
    * @return [json] user object
    */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
    *
    * @return [string] message
    */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
        'message' => 'Successfully logged out'
        ]);

    }
    
    public function getAllUsers(){
        $users = User::all();
        return response()->json($users);
    }

    public function getUserByPage(){
        $users = User::paginate(3);
        return response()->json($users);
    }

    public function getUserById( $id ){
        $user = User::where('id', $id)->first();
        if($user){
            return response()->json($user);
        }
        else{
            return response()->json([
                'message' => 'User Does Not Exits'
            ]);
        }
    }
    
    public function updateUser( Request $request, $id ){
        
        $validatedData = $request->validate([
            'warehouse_id' => 'required|int',
            'role' => 'required|string',
            'salary' => 'int',
        ]);

        $employee = Employee::find($id);
        if($employee){
            $user = User::find($employee->user_id);
            if($user){
                //Employee
                $employee->warehouse_id = $validatedData['warehouse_id'];
                $employee->salary = $validatedData['salary'];
                $employee->save();

                //Account
                $user->role = $validatedData['role'];
                $user->save();

                if($employee->save() && $user->save()){
                    return response()->json([
                        'message' => 'Saved Successfully',
                    ]);
                }
                else{
                    return response()->json([
                        'message' => 'Something went wrong'
                    ],500);
                }

            }else{
                return response()->json([
                    'message' => 'Account does not exits'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Employee does not exits'
            ],404);
        }
    }
}
