<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SupplierController extends Controller
{
    //
    public function getSuppliers(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
            $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

            $suppliers = Supplier::paginate($perPage, ['*'], 'page', $currentPage);

            $total = $suppliers->total(); // Total number of users matching the query
            $totalPage = ceil($total / $perPage); // Calculate total pages

            $cities = City::all();

            return response()->json([
                'cities' => $cities,
                'suppliers' => $suppliers,
                'total' => $total,
                'totalPage' => $totalPage,
            ]);
        }catch (\Exception $e){
            throw new BadRequestHttpException($e->getMessage());

        }

    }

    public function create(Request $request): JsonResponse
    {
        // Validate the request data (you can customize validation rules)
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => [
                'email',
                'string',
                'max:255',
                'nullable'
            ],
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'NRC' => 'nullable|date',
            'NART' => 'nullable|string',
            'NIS' => 'nullable|string',
            'NIF' => 'nullable|integer',
            'city_id' => 'required|integer',
        ];

        // Wrap the code inside a database transaction
        try {
            DB::beginTransaction();

            // call validator
            $request->validate($rules);

            // Create the new admin
            Supplier::create($request->all());

            // Commit the transaction
            DB::commit();

            return $this->fsSuccess("Supplier created successfully");
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollback();

            // Handle the exception, return an error response
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function update(Request $request): JsonResponse
    {
        // Validate the request data (you can customize validation rules)
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'string|max:255',
            'email' => [
                'email',
                'string',
                'max:255',
                'nullable'
            ],
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'NRC' => 'nullable|date',
            'NART' => 'nullable|string',
            'NIS' => 'nullable|string',
            'NIF' => 'nullable|integer',
            'city_id' => 'required|integer',
        ];

        // Wrap the code inside a database transaction
        try {
            DB::beginTransaction();

            // call validator
            $request->validate($rules);

            // update the new supplier
            $supplier = Supplier::find($request->input('id'));

            $supplier->update($request->all());

            // Commit the transaction
            DB::commit();

            return $this->fsSuccess("Supplier updated successfully");
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollback();

            // Handle the exception, return an error response
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function delete(Request $request) {
        try {

            $id = $request->input('id');

            $supplier = Supplier::find($id);

            $supplier->delete();

            return $this->fsSuccess("Supplier deleted successfully");
        }catch (\Exception $e){
            DB::rollBack();

            // Handle the exception, return an error response
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
