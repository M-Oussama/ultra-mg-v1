<?php

namespace App\Models;

use App\Http\Controllers\AttendanceController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'birthdate',
        'birthplace',
        'email',
        'address',
        'phone',
        'NIN',
        'NCN',
        'CNAS',
        'card_issue_date',
        'card_issue_date',
        'active'
    ];
    protected $casts = [
        'active' => 'boolean',
    ];

    //protected $with = ['employeeCareer'];

    public function attendance(){
        return $this->hasMany(EmployeeAttendance::class,'employee_id');
    }

    public function employeeCareer() {
        return $this->hasMany(EmployeeCareer::class);
    }

    public function addSchedule($year, $month){
        $attendanceController = new AttendanceController();
        $dates = $attendanceController->getDatesOfMonth($year, $month);
        $formattedDates = [];
        foreach ($dates as $date){
            $formattedDates[] = [
                'date'    => $date,
                'present' => true,
            ];
        }
        $this->workingDays = $formattedDates;
        $this->allDays = false;
        $this->quitEmployee = false;
        $this->quitDate = "";

    }
}
