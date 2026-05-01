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

        $user = auth()->user();
        $maintenancesQuery = Maintenance::query();
        $usersQuery = User::query();

        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                // Filter maintenances by technicians in the same departments
                $maintenancesQuery->whereHas('technician', function($q) use ($deptIds) {
                    $q->whereHas('departments', function($sq) use ($deptIds) {
                        $sq->whereIn('departments.id', $deptIds);
                    });
                })->orWhere('technician_id', $user->id)
                  ->orWhere('technician_assigned_id', $user->id);
                
                $usersQuery->whereHas('departments', function($q) use ($deptIds) {
                    $q->whereIn('departments.id', $deptIds);
                });
            } else {
                $maintenancesQuery->where('technician_id', $user->id)
                                  ->orWhere('technician_assigned_id', $user->id);
                $usersQuery->where('id', $user->id);
            }
        }

        $maintenances = $maintenancesQuery->get();
        $users = $usersQuery->where('id', '!=', 1)->get();
        $assets = Asset::all();
        return response()->json(['maintenances' => $maintenances, 'assets' => $assets, 'users'=>$users]);
    }

    public function store(Request $request) {


        $user = auth()->user();

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

        if($user && $user->isGlobalAdmin()){
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
        $user = auth()->user();
        $maintenance = Maintenance::find($id);

        if($user && $user->isGlobalAdmin()){
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
