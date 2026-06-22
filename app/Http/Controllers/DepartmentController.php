<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if ($user->isGlobalAdmin()) {
                $departments = Department::all();
            } else {
                $departments = $user->departments;
            }

            return response()->json([
                'success' => true,
                'departments' => $departments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('perPage', 10);
            $currentPage = $request->input('currentPage', 1);

            $user = auth()->user();

            if ($user->isGlobalAdmin()) {
                $departments = Department::paginate($perPage, ['*'], 'page', $currentPage);
            } else {
                $departments = $user->departments()->paginate($perPage, ['*'], 'page', $currentPage);
            }

            return response()->json([
                'success' => true,
                'departments' => $departments->items(),
                'total' => $departments->total(),
                'totalPage' => $departments->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'logo_url' => 'nullable|string|max:2048',
            'profession' => 'nullable|string|max:255',
            'department_type' => 'nullable|in:production,resell',
        ]);

        try {
            DB::beginTransaction();
            Department::create([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'logo_url' => $request->input('logo_url'),
                'profession' => $request->input('profession'),
                'department_type' => $request->input('department_type', 'resell'),
            ]);
            DB::commit();

            return $this->fsSuccess("Department created successfully");
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255|unique:departments,name,' . $request->input('id'),
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'logo_url' => 'nullable|string|max:2048',
            'profession' => 'nullable|string|max:255',
            'department_type' => 'nullable|in:production,resell',
        ]);

        try {
            DB::beginTransaction();
            $department = Department::find($request->input('id'));
            $department->update([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'logo_url' => $request->input('logo_url'),
                'profession' => $request->input('profession'),
                'department_type' => $request->input('department_type', $department->department_type ?? 'resell'),
            ]);
            DB::commit();

            return $this->fsSuccess("Department updated successfully");
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|exists:departments,id',
        ]);

        try {
            $id = $request->input('id');
            $department = Department::find($id);

            // Check if used in other tables (following the prompt's request to add it to sales, clients, products)
            $isUsedInSales = DB::table('sales')->where('department_id', $id)->exists();
            $isUsedInClients = DB::table('clients')->where('department_id', $id)->exists();
            $isUsedInProducts = DB::table('products')->where('department_id', $id)->exists();

            if ($isUsedInSales || $isUsedInClients || $isUsedInProducts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department because it is associated with sales, clients or products.'
                ], 400);
            }

            $department->delete();
            return $this->fsSuccess("Department deleted successfully");
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
