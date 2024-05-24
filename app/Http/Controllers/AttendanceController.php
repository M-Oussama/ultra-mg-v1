<?php

namespace App\Http\Controllers;

use App\Mail\NewEmployeeMail;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeCareer;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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

        try{
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
                $employeeRecord = EmployeeCareer::where('employee_id',$employee->employee_id)->where('start_date','<=', $lastDayOfMonth)
                    ->where(function ($query) use ($lastDayOfMonth, $firstDayOfMonth) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', $firstDayOfMonth);
                    })->get()->first();


                foreach($dates as $day){
                    $att = EmployeeAttendance::create([
                        'date'=> $day,
                        'attendance_id'=> $attendance->id,
                        'employee_id'=> $employee->employee_id,
                        'employee_career_id' => $employeeRecord->id,
                        'present' => false
                    ]);

//                    $att->end_date = $employee->end_date;
//                    $att->date = $employee->start_date;
                    $att->save();
                }
            }
        }catch (\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
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

        $employees_Id = EmployeeCareer::where('start_date','<=',$lastDayOfMonth) ->where(function ($query) use ($lastDayOfMonth, $firstDayOfMonth) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>=', $firstDayOfMonth);
        })->get()->pluck('employee_id');



        foreach ($employees_Id as $Empid) {

            if(!EmployeeAttendance::where('employee_id', $Empid)->where('attendance_id', $id)->exists()){
                $employeeRecord = EmployeeCareer::where('employee_id',$Empid)->where('start_date','<=', $lastDayOfMonth)
                    ->where(function ($query) use ($lastDayOfMonth, $firstDayOfMonth) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', $firstDayOfMonth);
                    })->get()->first();
                $dates_between = $this->get_dates_between($firstDayOfMonth, $lastDayOfMonth);
                foreach ($dates_between as $date){
                    EmployeeAttendance::create([
                        'attendance_id' => $id,
                        'employee_id' => $Empid,
                        'present' => 0,
                        'date'=> $date,
                        'employee_career_id' => $employeeRecord->id
                    ]);
                }


            }

        }



        // $attendance_employee = EmployeeAttendance::whereIn('employee_id',$employees_Id)->where('attendance_id',$id)->get();

        $attendance_employee = EmployeeAttendance::with(['employee', 'career'])
            ->where('attendance_id',$id)
            ->whereIn('employee_id',$employees_Id)
            ->orderBy('date')->get()
            ->groupBy('employee_id')
            ->map(function ($items, $employee_id) use ($firstDayOfMonth, $lastDayOfMonth) {
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
        $employeeRecord = EmployeeCareer::where('employee_id', $employeeId)->where('start_date','<=', $dates[count($dates)-1])->whereIsNull('end_date')->orwhere('end_date','>=',
            $dates[0])->get()->first();


        $employeeAttendance = [];
        if(!$attendance_exists) {
            $attendance = Attendance::find($attendanceId);
            $dates = $this->getDatesOfMonth($attendance->year, $attendance->month);
            $employeeRecord = EmployeeCareer::where('employee_id', $employeeId)->where('start_date','<=', $dates[count($dates)-1])->whereIsNull('end_date')->orwhere('end_date','>=',
            $dates[0])->get()->first();

            foreach ($dates as $day) {
                $employeeAttendance[] = EmployeeAttendance::create([
                    'date' => $day,
                    'present' => false,
                    'employee_id' => $employeeId,
                    'attendance_id' => $attendanceId,
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

        try {
            $searchValue = $request->input('searchValue', ''); // search value
            $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
            $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

            $employees = EmployeeCareer::where('employee_id', $Id)->orderBy('start_date','desc')->paginate($perPage, ['*'], 'page', $currentPage);

            foreach ($employees as $employee) {
                if(count($employee->getMedia("birth_certificate")) > 0)
                    $employee->BC = $employee->getMedia("birth_certificate")[0]->getUrl();
                if(count($employee->getMedia("national_card")) > 0)
                    $employee->NC = $employee->getMedia("national_card")[0]->getUrl();
            }


            $totalEmployees = $employees->total(); // Total number of users matching the query
            $totalPage = ($totalEmployees / $perPage); // Calculate total pages

            $latestEmployee = $employees->first();
            if ($latestEmployee) {
                $latestEmployee->last = true;
            }

            return response()->json(['employees' => $employees, 'totalEmployees'=> count($employees), '$totalPage'=>$totalPage]);

        }catch (\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }

    }

    public function updateEndDate(Request $request, $Id){

        $employee_career = EmployeeCareer::find($Id);
        $end_date = $request->input('endDate');
        $position = $request->input('position');
        $real_start_date = $request->input('real_start_date');
        $real_end_date = $request->input('real_end_date');

        try {
            $rules = [
                'endDate' => 'date|after:startDate',
                'real_end_date' => 'date|after:real_start_date',
            ];

            if($real_end_date) {
                if(!$real_start_date){
                    throw new BadRequestHttpException('Real start date is missing');
                }
                if($real_start_date >= $real_end_date){
                    throw new BadRequestHttpException('Real end Date must be after the Real start Date');
                }
            }

            if($end_date){
                if($employee_career->start_date >= $end_date){

                    throw new BadRequestHttpException('End Date must be after the start Date');

                }else {
                    $request->validate($rules);

                   $employee_career->update(['end_date' => $end_date , "real_end_date" => $real_end_date, "real_start_date"=> $real_start_date, "position" => $position]);
                }
            }else {
                $employee_career->update(['position' => $position, 'end_date' => null, "real_end_date" => $real_end_date, "real_start_date"=> $real_start_date]);
            }



            return response()->json(['endDate' => $end_date, 'msg' =>'End Date Updated Successfully']);
        }catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }





    }
    public function NewEmployeeAttendanceRecord(Request $request, $Id){

        $rules = [
            'startDate' => 'required|date',
        ];
        $request->validate($rules);
        $end_date = $request->input('endDate',null);
        $start_date = $request->input('startDate');
        $position = $request->input('position');
        $birth_certificateB64 = $request->input('birth_certificate');
        $national_cardB64 = $request->input('national_card');
        $real_start_date= $request->input('real_start_date');
        $real_end_date = $request->input('real_end_date');

        $employeeData = Employee::find($Id);

        try{
            DB::beginTransaction();
            $record = EmployeeCareer::where('employee_id', $Id)->orderBy('created_at', 'desc')->first();

            if($end_date != null) {
                if($record){
                    if($record->end_date) {
                        if($record->end_date >= $start_date ){
                            throw new BadRequestHttpException('Start Date must be after the latest end date of the employee');
                        }

                    }else{
                        throw new BadRequestHttpException('You can not add a new record until the previous one is ended');
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

            }else {
                if($record){
                    if($record->end_date == null){
                        throw new BadRequestHttpException('You can not add a new record until the previous one is ended');

                    }
                }
            }




            $carrer =  EmployeeCareer::create([
                'employee_id'=>$Id,
                'start_date' => $start_date,
                'end_date' => $end_date ? $end_date : null,
                'position' => $position,
                'real_start_date' => $real_start_date ? $real_start_date: null,
                'real_end_date' => $real_end_date ? $real_end_date: null
            ]);


            if($birth_certificateB64){
                $birth_certificate_name = 'BC'.$employeeData->name.'-'.$employeeData->surname.'.pdf';

                list($type, $BCbase64code) = explode(';', $birth_certificateB64);
                list(,$BCbase64code) = explode(',' , $BCbase64code);
                $carrer->addMediaFromBase64($BCbase64code)
                    ->usingFileName($birth_certificate_name)
                    ->toMediaCollection('birth_certificate');
            }



            if($national_cardB64){
                $national_card_name = 'NC'.$employeeData->name.'-'.$employeeData->surname.'.pdf';

                list($type, $NCbase64code) = explode(';', $national_cardB64);
                list(,$NCbase64code) = explode(',' , $NCbase64code);
                $carrer->addMediaFromBase64($NCbase64code)
                    ->usingFileName($national_card_name)
                    ->toMediaCollection('national_card');
            }



            $carrer->save();
            DB::commit();
            return response()->json([ 'msg' =>'Record created Successfully']);

        }catch (\Exception $e){
            DB::rollBack();
            throw new BadRequestHttpException($e->getMessage());
        }

    }

    /**
     * Get file content from base64 string.
     *
     * @param $data
     * @return string
     */
    private function getBase64Content($data)
    {
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        return $data;
    }

    /**
     * Saves temporary file from base64 string.
     *
     * @param $filename
     * @param $data
     * @return File
     */
    private function saveFileFromBase64($filename, $data)
    {
        $data = $this->getBase64Content($data);
        file_put_contents($filename, $data);
        $file = new File($filename);

        return $file;
    }

    public function deleteEmployeeCareer(Request $request, $Id) {
        $career = EmployeeCareer::find($Id);

        // if the employee has no attendance in this career
        //$attendance = EmployeeAttendance::
        $career->delete();
        return response()->json(['message' =>'Record deleted Successfully']);

    }

    public function generateEmail(Request $request, $careerId){
        $email = $request->input('email');
        $career = EmployeeCareer::with('employee')->where('id',$careerId)->get()->first();

        $career->BC = $career->getMedia("birth_certificate")[0]->getPath();
        $career->NC = $career->getMedia("national_card")[0]->getPath();
        Mail::to($email)->send(new NewEmployeeMail($career->employee->name, $career->employee->surname, $career->position, $career->start_date, $career->BC, $career->NC));
        return $this->fsSuccess('Email Sent Successfully To '.$email);

    }
}
