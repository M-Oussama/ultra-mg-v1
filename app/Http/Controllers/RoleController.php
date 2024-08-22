<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermissions;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoleController extends Controller
{
    public function list(Request $request){
        $roles = Role::all();
        $permissions = Permission::all();
        return response()->json(['roles' => $roles, 'permissions' => $permissions]);
    }

    public function store(Request $request) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'role' => 'string|max:255|unique:roles',
        ]);

        $permissions = $request->input('permissions');

        // Create a new  record in the database using Role::create()
        $role = Role::create([
            'role' => $validatedData['role'],
        ]);

        foreach ($permissions as $permission) {
            RoleHasPermissions::create([
                'role_id' => $role->id,
                'permission_id' => $permission['id']
            ]);
        }

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Role created successfully', 'role' => $role]);
    }


    public function update(Request $request, $id) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'role' => 'string|max:255',
        ]);

        $roleExists = Role::where('role', $validatedData['role'])->where('id', '!=', $id)->exists();

        if($roleExists) {
            throw new BadRequestHttpException('Role Name Already Exists');
        }
        $permissions = $request->input('permissions');

        $role = Role::find($id);
        // Create a new  record in the database using Role::create()
        $role->update([
            'role' => $validatedData['role'],
        ]);

       RoleHasPermissions::where('role_id', $id)->delete();

        foreach ($permissions as $permission) {
            RoleHasPermissions::create([
                'role_id' => $role->id,
                'permission_id' => $permission['id']
            ]);
        }

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Role updated successfully', 'role' => $role]);
    }

    public function delete($id) {
        RoleHasPermissions::where('role_id', $id)->delete();

        Role::find($id)->delete();

        return response()->json(['message' => 'Role deleted successfully']);

    }

}
