<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCareer;
use App\Models\YearlyVacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VacationController extends Controller
{
    public function getVacations(Request $request){


        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $employee_id = $request->input('id', null); // Default current page value is 1 if not provided
        $vacations = YearlyVacation::orderBy('start_date', 'desc');
        $user = auth()->user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $vacations->whereHas('employee', function($q) use ($deptIds) {
                    $q->whereIn('department_id', $deptIds);
                });
            } else {
                $vacations->whereHas('employee', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

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

    /**
     * Import yearly_vacations from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);
        foreach (['id', 'start_date', 'end_date', 'count', 'employee_id', 'employee_career_id'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'start_date' => $rowData['start_date'] ?? null,
                'end_date' => $rowData['end_date'] ?? null,
                'count' => isset($rowData['count']) ? (int) $rowData['count'] : 0,
                'employee_id' => isset($rowData['employee_id']) ? (int) $rowData['employee_id'] : null,
                'employee_career_id' => isset($rowData['employee_career_id']) ? (int) $rowData['employee_career_id'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'count' => 'required|integer|min:0',
                'employee_id' => 'required|integer',
                'employee_career_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            if (!empty($payload['id']) && DB::table('yearly_vacations')->where('id', $payload['id'])->exists()) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Yearly vacation id exists, inserted with auto-generated id.'];
                unset($payload['id']);
            }

            DB::table('yearly_vacations')->insert($payload);
            $inserted++;
        }

        fclose($handle);
        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }
}
