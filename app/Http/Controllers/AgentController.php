<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|string',
        ]);

        $agent = new Agent([
            'name' => $request->name,
            'city' => $request->city,
            'address' => $request->address,
            'status' => $request->status,
        ]);
        if ($agent->save()) {
            return response()->json([
                'message' => 'Agent Added Successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Something Went Wrong'
            ], 500);
        }
    }

    public function getByPage(){
        $agents = Agent::where('status','Cooperating')->paginate(5);
        return response()->json($agents);
    }

    public function all(){
        $agents = Agent::where('status','Cooperating')->get();
        return response()->json($agents);
    }

    public function getById( $id ){
        $agent = Agent::find( $id );
        if($agent){
            return response()->json($agent);
        }
        else{
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }
    }

    public function agentsFilter(Request $request){
        $request->validate([
            'status' => 'string',
            'from' => 'date',
            'to' => 'date'
        ]);
        $query = DB::table('agents');

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

    public function update( Request $request, $id ){
        $validatedData = $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|string',
        ]);

        $agent = Agent::find($id);
        if($agent){
            $agent->name = $validatedData['name'];
            $agent->city = $validatedData['city'];
            $agent->address = $validatedData['address'];
            $agent->status = $validatedData['status'];

            if($agent->save()){
                return response()->json([
                    'message' => 'Agent Update Successfully'
                ]);
            }
            else{
                return response()->json([
                    'message' => 'Something went wrong'
                ]);
            }
        }
        else{
            return response()->json([
                'message' => 'Agent does not exits'
            ], 404);
        }
    }

    public function destroy($id){
        $agent = Agent::find($id);
        if (!$agent) {
            return response()->json([
                'message' => 'Agent does not exist'
            ], 404);
        }
    
        if ($agent->delete()) {
            return response()->json([
                'message' => 'Agent deleted successfully'
            ]);
        }
    
        return response()->json([
            'message' => 'Something went wrong'
        ]);
    }
}
