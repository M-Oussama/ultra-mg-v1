<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    public function getEmployees(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $employees = Employee::when($searchValue, function ($queryBuilder) use ($searchValue) {
            // Search for users with matching name or email
            $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('surname', 'LIKE', '%' . $searchValue . '%');
        })->paginate($perPage, ['*'], 'page', $currentPage);
        $totalEmployees = $employees->total(); // Total number of users matching the query
        $totalPage = ceil($totalEmployees / $perPage); // Calculate total pages

        return response()->json(["employees" => $employees, "totalPage" => $totalPage, "totalEmployees"=>$totalEmployees]);
    }

    public function store(Request $request) {

        $employee_data = $request->input('employee');
        $employee = Employee::create($employee_data);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Employee created successfully', 'employee' => $employee]);
    }
}
