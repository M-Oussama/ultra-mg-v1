<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Company::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'address2' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'NRC' => 'required|string',
            'NIF' => 'required|string',
            'NART' => 'required|string',
            'NIS' => 'required|string',
            'capitale' => 'required|string',
            'show_company_info' => 'sometimes|boolean',
        ]);

        $validated['show_company_info'] = $request->boolean('show_company_info', true);

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): JsonResponse
    {
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'address2' => 'nullable|string',
            'phone' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'NRC' => 'sometimes|required|string',
            'NIF' => 'sometimes|required|string',
            'NART' => 'sometimes|required|string',
            'NIS' => 'sometimes|required|string',
            'capitale' => 'sometimes|required|string',
            'show_company_info' => 'sometimes|boolean',
        ]);

        if ($request->has('show_company_info')) {
            $validated['show_company_info'] = $request->boolean('show_company_info');
        }

        $company->update($validated);

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json([
            'message' => 'Company deleted successfully'
        ]);
    }
}
