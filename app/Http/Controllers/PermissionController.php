<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PermissionController extends Controller
{
    public function list(Request $request){
        $permissions = Permission::all();

        return response()->json(['permissions' => $permissions]);
    }
    public function store(Request $request) {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'action' => 'string|max:255',
            'subject' => 'string|max:255',
        ]);

        $permissionExists = Permission::where('action', $validatedData['action'])->where('subject', $validatedData['subject'])->exists();

        if($permissionExists) {
            throw new BadRequestHttpException('Permission Already Exists');
        }
        // Create a new  record in the database using Role::create()
        $permission = Permission::create([
            'action' => $validatedData['action'],
            'subject' => $validatedData['subject'],
        ]);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Permission created successfully', 'permission' => $permission]);
    }

    public function update(Request $request, $id) {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'action' => 'string|max:255',
            'subject' => 'string|max:255',
        ]);

        $permission = Permission::find($id);

        $permissionExists = Permission::where('action', $validatedData['action'])->where('subject', $validatedData['subject'])->where('id', '!=', $id)->exists();

        if($permissionExists) {
            throw new BadRequestHttpException('Permission Already Exists');
        }
        // Create a new  record in the database using Role::create()
        $permission->update([
            'action' => $validatedData['action'],
            'subject' => $validatedData['subject'],
        ]);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Permission updated successfully', 'permission' => $permission]);
    }

    public function delete($id){
       Permission::find($id)->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
