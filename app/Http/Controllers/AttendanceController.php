<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AttendanceController extends Controller
{
    //
    //
    public function getAttendances(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $attendances = Attendance::when($searchValue, function ($queryBuilder) use ($searchValue) {
            // Search for users with matching name or email
            $queryBuilder->where('month', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('year', 'LIKE', '%' . $searchValue . '%');
        })->paginate($perPage, ['*'], 'page', $currentPage);
        $totalAttendances = $attendances->total(); // Total number of users matching the query
        $totalPage = ceil($totalAttendances / $perPage); // Calculate total pages
        $months = $this->months(1);
        $years = $this->years(2000);

        return response()->json(["attendances" => $attendances,"months"=>$months, "years"=>$years, "totalPage" => $totalPage, "totalAttendances"=>$totalAttendances]);
    }

    public function store(Request $request){
        $attendance = $request->input('attendance');
        $month = $attendance['month'];
        $year = $attendance['year'];

        $exists = Attendance::where('month',$month)->where('year',$year)->exists();
        if($exists){
            throw new BadRequestHttpException('The Selected Month/Year Already Exists');
        }

        $attendance =  Attendance::create([
            'month'=>$month,
            'year'=>$year
        ]);

        $employees = Employee::where('active',true)->get();

        $dates = $this->getDatesOfMonth($attendance->year, $attendance->month);

        foreach ($employees as $employee) {
            foreach($dates as $day){
                EmployeeAttendance::create([
                    'date'=> $day,
                    'attendance_id'=> $attendance->id,
                    'employee_id'=> $employee->id,
                    'present' => false
                ]);
            }
        }

        return $this->responseMessage("Attendance created Successfully");
    }

    public function getAttendanceData(Request $request, $id): JsonResponse {
        $attendance = Attendance::find($id);
        $dates = $this->getDatesOfMonth($attendance->year, $attendance->month);
        $employees = Employee::all();


        return response()->json(["employees" => $employees, "dates"=> $dates]);
    }
}
