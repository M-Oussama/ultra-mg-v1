<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeCareer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
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
        $firstDayOfMonth = date("Y-m-d", strtotime("$attendance->year-$attendance->month-01"));
        $lastDayOfMonth = date("Y-m-t", strtotime("$attendance->year-$attendance->month-01"));

        $employees = EmployeeCareer::where('start_date','<=',$lastDayOfMonth)->orwhere('end_date',Null)->orWhere('end_date','>=',$firstDayOfMonth)->get();

        foreach ($employees as $employee) {
            $dates = $this->getDatesIntervale($employee->start_date, $employee->end_date, $attendance->month,$attendance->year);
            foreach($dates as $day){
                $att = EmployeeAttendance::create([
                    'date'=> $day,
                    'attendance_id'=> $attendance->id,
                    'employee_id'=> $employee->employee_id,

                    'present' => false
                ]);

                $att->end_date = $employee->end_date;
                $att->start_date = $employee->start_date;
                $att->save();
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
            foreach($employeeAttendance['result'] as $result) {
                foreach ($result as $attendance) {
                    $_attendance = EmployeeAttendance::find($attendance['id']);
                    $_attendance->present = $attendance['present'];
                    $_attendance->save();
                }
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
        $employeesActive = Employee::whereHas('employeeCareer', function ($query) use ($lastDayOfMonth,$firstDayOfMonth) {
            $query->where('end_date', '>=', $firstDayOfMonth)->orWhere('end_date', '=', null);

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
        $lastDayOfMonth = date("Y-m-t", strtotime("$attendance->year-$attendance->month-01"));

        $firstDayOfMonth = date("Y-m-d", strtotime("$attendance->year-$attendance->month-01"));

        $employees_Id = EmployeeCareer::where('start_date','>=',$firstDayOfMonth)->get()->pluck('employee_id');


        // $attendance_employee = EmployeeAttendance::whereIn('employee_id',$employees_Id)->where('attendance_id',$id)->get();

        $attendance_employee = EmployeeAttendance::with('employee')->where('attendance_id',$id)->whereIn('employee_id',$employees_Id)->orderBy('date')->get()->groupBy('employee_id')->map(function ($items, $employee_id) use ($firstDayOfMonth, $lastDayOfMonth) {
            $presentCount = $items->where('present', true)
                ->where(function ($item) {
                    // Use Carbon to get the day of the week
                    $dayOfWeek = Carbon::parse($item->date)->dayOfWeek;

                    // Check if the day is not Thursday (4) or Friday (5)
                    return !in_array($dayOfWeek, [Carbon::THURSDAY, Carbon::FRIDAY]);
                })
                ->count();
            $absentCount = $items->where('present', false)->count();


            return [
                'id' => $employee_id,
                'employee' => Employee::find($employee_id),
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'end_date'=> '',
                'result' => $items->groupBy('end_date')->toArray(), // or $items->pluck('column_name') if you want specific columns
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

    public function fetchEmployeesByAttendance(Request $request, $Id){
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $employees = EmployeeCareer::where('employee_id', $Id)->orderBy('start_date','desc')->paginate($perPage, ['*'], 'page', $currentPage);
        $totalEmployees = $employees->total(); // Total number of users matching the query
        $totalPage = ($totalEmployees / $perPage); // Calculate total pages

        $latestEmployee = $employees->first();
        if ($latestEmployee) {
            $latestEmployee->last = true;
        }

        return response()->json(['employees' => $employees, 'totalEmployees'=> count($employees), '$totalPage'=>$totalPage]);

    }

    public function updateEndDate(Request $request, $Id){

        $employee_career = EmployeeCareer::find($Id);
        $rules = [
            'endDate' => 'required|date|after:startDate',
        ];
        $end_date = $request->input('endDate');

        if($employee_career->start_date >= $end_date){

            throw new BadRequestHttpException('End Date must be after the start Date');

        }

        $request->validate($rules);



        $employee_career->update(['end_date' => $end_date]);

        return response()->json(['endDate' => $end_date, 'msg' =>'End Date Updated Successfully']);
    }
    public function NewEmployeeAttendanceRecord(Request $request, $Id){
        $rules = [
            'startDate' => 'required|date',
        ];
        $request->validate($rules);
        $end_date = $request->input('endDate',null);
        $start_date = $request->input('startDate');

        $record = EmployeeCareer::orderBy('created_at', 'desc')->first();

        if(!$end_date) {
            if($record){
                if($record->end_date) {
                    if($record->end_date >= $start_date ){
                        throw new BadRequestHttpException('Start Date must be after the latest end date of the employee');
                    }

                }else{
                    throw new BadRequestHttpException('You can not add a new record until the previous one is ended');
                }
            }
        }


// Condition 1: new_start_date < start_date and new_end_date < end_date
        $condition1 = EmployeeCareer::
            where('employee_id', $Id)
            ->where('start_date', '<', $start_date)
            ->where('end_date', '>', $end_date)
            ->exists();

// Condition 2: new_start_date < start_date and new_end_date > end_date
        $condition2 = EmployeeCareer::
            where('employee_id', $Id)
            ->where('start_date', '>', $start_date)
            ->where('end_date', '<', $end_date)
            ->exists();

        $condition3 = EmployeeCareer::
        where('employee_id', $Id)
            ->where('start_date', '>', $start_date)
            ->where('end_date', '>', $end_date)
            ->where('start_date', '<=', $end_date)
            ->exists();

// Condition 4: new_start_date > start_date and new_end_date > end_date
        $condition4 = EmployeeCareer::
            where('employee_id', $Id)
            ->where('start_date', '<', $start_date)
            ->where('end_date', '>=', $start_date)
            ->where('end_date', '<', $end_date)
            ->exists();



        $overlap = EmployeeCareer::where('employee_id', $Id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->where(function ($q) use ($start_date, $end_date) {
                    $q->where('start_date', '<=', $start_date)
                        ->where(function ($qq) use ($start_date) {
                            $qq->whereNull('end_date')
                                ->orWhere('end_date', '>=', $start_date);
                        });
                })
                    ->orWhere(function ($q) use ($start_date, $end_date) {
                        $q->where('start_date', '<=', $end_date)
                            ->where(function ($qq) use ($end_date) {
                                $qq->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $end_date);
                            });
                    });
            })
            ->exists();


       

        if($overlap || $condition1 || $condition2 || $condition3 || $condition4) {
            throw new BadRequestHttpException('The Date Intervale is wrong choose a correct one ');

        }

        EmployeeCareer::create([
            'employee_id'=>$Id,
            'start_date' => date("Y-m-d", strtotime($start_date)),
            'end_date' => $end_date ? date("Y-m-d", strtotime($end_date)) : null,
        ]);

        return response()->json([ 'msg' =>'Record created Successfully']);
    }

    public function deleteEmployeeCareer(Request $request, $Id) {
        $career = EmployeeCareer::find($Id);

        // if the employee has no attendance in this career
        //$attendance = EmployeeAttendance::
        $career->delete();
        return response()->json(['message' =>'Record deleted Successfully']);

    }
}
