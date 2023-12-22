<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeCareer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $employee = Employee::all();
        return response()->json(["attendances" => $attendances, "employee"=>$employee,"months"=>$months, "years"=>$years, "totalPage" => $totalPage, "totalAttendances"=>$totalAttendances]);
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

    public function submit(Request $request) {

        $employees = $request->input('employees');
        $attendanceId = $request->input('attendanceId');
        $attendance = Attendance::find($attendanceId);

        foreach ($employees as $employee) {
            foreach ($employee['workingDays'] as $workingDay) {
               EmployeeAttendance:: create([
                    'date' => $workingDay['date'],
                    'present' => $workingDay['present'],
                    'employee_id' => $employee['id'],
                    'attendance_id' => $attendanceId
                ]);
            }
            $_employee = Employee::find($employee['id']);
            $_employee->active = !$employee['quitEmployee'];
            $_employee->save();

            if($employee['quitEmployee']) {
                $employeeCareer = EmployeeCareer::where('employee_id',$employee['id'])->where('end_date',null)->get()->first();
                $employeeCareer->end_date = $employee['quitDate'];
                $employeeCareer->save();
            }
        }

        $attendance->active = true;
        $attendance->save();

        return response()->json(["message"=>"Attendance Submitted Successfully"]);


    }

    public function update(Request $request) {
        $employeeAttendances = $request->input('attendances');

        foreach ($employeeAttendances as $employeeAttendance){

            foreach ($employeeAttendance['result'] as $attendance) {

                $_attendance = EmployeeAttendance::find($attendance['id']);
                $_attendance->present = $attendance['present'];
                $_attendance->save();
            }
        }

        return response()->json(["message"=>"Attendance Updated Successfully"]);


    }

    public function getAttendanceData(Request $request, $id): JsonResponse {
        $attendance = Attendance::find($id);
        $dates = $this->getDatesOfMonth($attendance->year, $attendance->month);

        foreach ($dates as $date){
            $formattedDates[] = [
                'date'    => $date,
                'present' => true,
            ];
        }
        $employeesActive = Employee::with('employeeCareer')->get();

        $lastDayOfMonth = date("Y-m-t", strtotime("$attendance->year-$attendance->month-01"));

        $firstDayOfMonth = date("Y-m-d", strtotime("$attendance->year-$attendance->month-01"));


        $employeesActive = Employee::whereHas('employeeCareer', function ($query) use ($lastDayOfMonth,$firstDayOfMonth) {
            $query->where('start_date', '<=', $lastDayOfMonth)
                ->where(function ($query) use ($lastDayOfMonth, $firstDayOfMonth) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $firstDayOfMonth);
                });
        })->with('employeeCareer')->get();

        $employeesNonActive = Employee::where('active',false)->get();

        foreach ($employeesActive as $employee) {
            $employee->addSchedule($attendance->year,$attendance->month);
        }
        foreach ($employeesNonActive as $employee) {
            $employee->addSchedule($attendance->year,$attendance->month);
        }

        return response()->json([
            "employees" => $employeesActive,
            "restEmployees"=>$employeesNonActive,
            "dates"=> $formattedDates,
            "minDate" => $firstDayOfMonth,
            "maxDate" => $lastDayOfMonth
        ]);
    }

    public function getAttendance(Request $request,$id){
        $attendance = Attendance::find($id);

        $attendance_employee = EmployeeAttendance::with('employee')->where('attendance_id',$id)->get()->groupBy('employee_id');

        $employee = Employee::all();
        return response()->json(["attendance"=>$attendance, "employees"=>$employee, "employeeAttendance"=>$attendance_employee]);

    }
    public function getAttendanceByID(Request $request,$id){
        $attendance = Attendance::find($id);

        $attendance_employee = EmployeeAttendance::with('employee')->where('attendance_id',$id)->orderBy('date')->get()->groupBy('employee_id')->map(function ($items, $employee_id) {
            $presentCount = $items->where('present', false)->count();
            $absentCount = $items->where('present', true)->count();
            return [
                'id' => $employee_id,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'result' => $items->toArray(), // or $items->pluck('column_name') if you want specific columns
            ];
        });

        $employeesAttendance = EmployeeAttendance::select('employee_id')->where('attendance_id',$id)->pluck('employee_id')->unique()->values()->all();
        $employees = Employee::whereNotIn('id',$employeesAttendance)->get();
        return response()->json(["attendance"=>$attendance, "employees"=>$employees, "employeeAttendance"=>$attendance_employee]);

    }

    public function AddEmployeeToAttendance(Request $request) {

        $employeeId = $request->input('employee');
        $attendanceId = $request->input('attendanceId');

        $attendance_exists = EmployeeAttendance::where('attendance_id',$attendanceId)->where('employee_id',$employeeId)->exists();

        $employeeAttendance = [];
        if(!$attendance_exists) {
            $attendance = Attendance::find($attendanceId);
            $dates = $this->getDatesOfMonth($attendance->year, $attendance->month);
            foreach ($dates as $day) {
                $employeeAttendance[] = EmployeeAttendance::create([
                    'date' => $day,
                    'present' => false,
                    'employee_id' => $employeeId,
                    'attendance_id' => $attendanceId
                ]);
            }
        }
        $newAttendance = [

                'id' => $employeeId,
                'result' => $employeeAttendance,

        ];


        $employeesAttendance = EmployeeAttendance::select('employee_id')->where('attendance_id',$attendanceId)->pluck('employee_id')->unique()->values()->all();
        $employees = Employee::whereNotIn('id',$employeesAttendance)->get();


        return response()->json(["key"=> $employeeId, "newAttendance"=>$newAttendance, "employees" => $employees]);


    }

    public function RemoveEmployeeFromAttendance(Request $request) {

        $attendances = $request->input('attendance');
        $attendanceId = $request->input('attendanceId');

        $_attendances = EmployeeAttendance::where('attendance_id',$attendanceId)->where('employee_id',$attendances[0]['employee_id'])->get();

        foreach ($_attendances as $item) {
            $item->delete();
        }

        $employeesAttendance = EmployeeAttendance::select('employee_id')->where('attendance_id',$attendanceId)->pluck('employee_id')->unique()->values()->all();
        $employees = Employee::whereNotIn('id',$employeesAttendance)->get();


        return response()->json(["employees" => $employees]);


    }
}
