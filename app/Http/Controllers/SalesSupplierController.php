<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Department;
use App\Models\SalesSupplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SalesSupplierController extends Controller
{
    /**
     * Get a paginated list of sales suppliers with filtering.
     */
    public function getSuppliers(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('perPage', 10);
            $currentPage = $request->input('currentPage', 1);
            $departmentId = $request->input('departement_id');
            $searchValue = $request->input('searchValue');

            $query = SalesSupplier::with(['city', 'department']);

            if ($departmentId) {
                $query->where('departement_id', $departmentId);
            }

            if ($searchValue) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('surname', 'like', "%{$searchValue}%")
                      ->orWhere('full_name', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%")
                      ->orWhere('phone', 'like', "%{$searchValue}%");
                });
            }

            $suppliers = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $cities = City::all();
            $departments = Department::all();

            return response()->json([
                'success' => true,
                'suppliers' => $suppliers->items(),
                'cities' => $cities,
                'departments' => $departments,
                'total' => $suppliers->total(),
                'totalPage' => $suppliers->lastPage(),
            ]);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * Get data for initializing the create/edit form.
     */
    public function getData(): JsonResponse
    {
        return response()->json([
            'cities' => City::all(),
            'departments' => Department::all(),
        ]);
    }

    /**
     * Store a newly created sales supplier.
     */
    public function create(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'NRC' => 'nullable|string',
            'NIF' => 'nullable|string',
            'NART' => 'nullable|string',
            'NIS' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'departement_id' => 'required|exists:departments,id',
        ];

        try {
            DB::beginTransaction();

            $validatedData = $request->validate($rules);
            
            $supplier = SalesSupplier::create($validatedData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Supplier created successfully',
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * Update the specified sales supplier.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'NRC' => 'nullable|string',
            'NIF' => 'nullable|string',
            'NART' => 'nullable|string',
            'NIS' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'departement_id' => 'required|exists:departments,id',
        ];

        try {
            DB::beginTransaction();

            $validatedData = $request->validate($rules);
            
            $supplier = SalesSupplier::findOrFail($id);
            $supplier->update($validatedData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Supplier updated successfully',
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * Remove the specified sales supplier.
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $supplier = SalesSupplier::findOrFail($id);
            
            // Note: You might want to add checks here if this supplier is linked to other entities (e.g. sales, orders)
            // if we add those later.

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sales Supplier deleted successfully'
            ]);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
