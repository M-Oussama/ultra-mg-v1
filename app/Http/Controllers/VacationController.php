<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCareer;
use App\Models\YearlyVacation;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    public function getVacations(Request $request){

        dd($request);
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $employee_id = $request->input('id', null); // Default current page value is 1 if not provided
        $vacations = YearlyVacation::orderBy('start_date', 'desc');
        if($employee_id){
            $vacations->where('employee_id', $employee_id);
        }



        $vacations->paginate($perPage, ['*'], 'page', $currentPage);
        $totalVacations = $vacations->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalVacations / $perPage); // Calculate total pages

        return response()->json(["list" => $vacations, "totalPage" => $totalPage, "totalVacations"=>$totalVacations]);
    }
    public function getVacationsByEmployee(Request $request, $id){

        $employee_id = EmployeeCareer::find($id)->employee_id;
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $vacations = YearlyVacation::orderBy('start_date', 'desc')->where('employee_id', $employee_id)->paginate($perPage, ['*'], 'page', $currentPage);


        $totalVacations = $vacations->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalVacations / $perPage); // Calculate total pages

        return response()->json(["list" => $vacations, "totalPage" => $totalPage, "totalVacations"=>$totalVacations]);
    }
    public function store(Request $request, $id){

        $employee_career = EmployeeCareer::find($id);
        if($employee_career){
            $yearlyVacation = YearlyVacation::create([
                'start_date' => $request->input('startDate'),
                'end_date' => $request->input('endDate'),
                'count' => $request->input('count'),
                'employee_id' => $employee_career->employee_id,
                'employee_career_id' => $id,
            ]);
        }
        $this->fsSuccess('Record Created Successfully');

    }
    public function update(Request $request, $id){

        $vacation = YearlyVacation::find($id);

        $vacation->update([
                'start_date' => $request->input('startDate'),
                'end_date' => $request->input('endDate'),
                'count' => $request->input('count'),
            ]);
        return response()->json([
            "success" => true,
            "message" => "Record updated Successfully"
        ]);

    }

    public function destroy($id){
        $vacation = YearlyVacation::find($id);
        $vacation->delete();

        return response()->json([
            "success" => true,
            "message" => "Record Deleted Successfully"
        ]);
    }

    public function getVacation($id){
        $vacation = YearlyVacation::with(['employee', 'employee_career'])->where('id',  $id)->get()->first();

        return response()->json([
            "success" => true,
            "vacation" => $vacation
        ]);
    }
}
