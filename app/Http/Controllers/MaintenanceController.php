<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\Role;
use App\Models\User;

use Illuminate\Http\Request;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MaintenanceController extends Controller
{
    public function list(Request $request){

        $user = $this->getConnectedUser($request);
        $users = [];
        if($user->role_id == Role::ADMIN) {
            $maintenances = Maintenance::all();
            $users = User::where('role_id', '!=', 1)->get();
        }else{
            $maintenances = Maintenance::where('technician_id', $user->id)->orWhere('technician_assigned_id', $user->id)->get();
        }
        $assets = Asset::all();
        return response()->json(['maintenances' => $maintenances, 'assets' => $assets, 'users'=>$users]);
    }

    public function store(Request $request) {


        $user = $this->getConnectedUser($request);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'component_id' => 'required|integer',
            'asset_id' => 'required|integer',
            'technician_assigned_id' => 'integer|nullable',
            'maintenance_date' => 'required|date',
            'next_maintenance_date' => 'required|date',
            'notes' => 'string|nullable',
            'status' => 'required|string',
        ]);

        if($user->role_id == Role::ADMIN){
            // Create a new  record
            $maintenance = Maintenance::create([
                'name' => $validatedData['name'],
                'component_id' => $validatedData['component_id'],
                'asset_id' => $validatedData['asset_id'],
                'maintenance_date' => $validatedData['maintenance_date'],
                'next_maintenance_date' => $validatedData['next_maintenance_date'],
                'notes' => $validatedData['notes'],
                'status' => $validatedData['status'],
                'technician_id' => $user->id,
                'technician_assigned_id' => $validatedData['technician_assigned_id'],
            ]);
        }else {
            // Create a new  record
            $maintenance = Maintenance::create([
                'name' => $validatedData['name'],
                'component_id' => $validatedData['component_id'],
                'asset_id' => $validatedData['asset_id'],
                'maintenance_date' => $validatedData['maintenance_date'],
                'next_maintenance_date' => $validatedData['next_maintenance_date'],
                'notes' => $validatedData['notes'],
                'status' => $validatedData['status'],
                'technician_id' => $user->id,
                'technician_assigned_id' => $user->id,
            ]);
        }


        return response()->json(['message' => 'Operation created successfully', 'maintenance' => $maintenance]);

    }

    public function update(Request $request, $id) {


        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'component_id' => 'required|integer',
            'technician_assigned_id' => 'integer|nullable',
            'asset_id' => 'required|integer',
            'maintenance_date' => 'required|date',
            'next_maintenance_date' => 'required|date',
            'notes' => 'string|nullable',
            'status' => 'required|string',
        ]);
        $user = $this->getConnectedUser($request);
        $maintenance = Maintenance::find($id);

        if($user->role_id == Role::ADMIN){
            $maintenance->update([
                'name' => $validatedData['name'],
                'component_id' => $validatedData['component_id'],
                'asset_id' => $validatedData['asset_id'],
                'maintenance_date' => $validatedData['maintenance_date'],
                'next_maintenance_date' => $validatedData['next_maintenance_date'],
                'notes' => $validatedData['notes'],
                'status' => $validatedData['status'],
                'technician_assigned_id' => $validatedData['technician_assigned_id'],
            ]);
        }else {
            $maintenance->update([
                'name' => $validatedData['name'],
                'component_id' => $validatedData['component_id'],
                'asset_id' => $validatedData['asset_id'],
                'maintenance_date' => $validatedData['maintenance_date'],
                'next_maintenance_date' => $validatedData['next_maintenance_date'],
                'notes' => $validatedData['notes'],
                'status' => $validatedData['status'],
            ]);
        }


        return response()->json(['message' => 'Operation updated successfully']);

    }

    public function delete(Request $request, $id) {

        $maintenance = Maintenance::find($id);
        if($maintenance->technician_id != $maintenance->technician_assigned_id){
            throw new BadRequestHttpException('You can not delete this ticket');
        }
        Maintenance::find($id)->delete();

        return response()->json(['message' => 'Operation deleted successfully']);

    }


}
