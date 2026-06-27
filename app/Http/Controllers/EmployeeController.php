<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    //
    public function getEmployees(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $query = Employee::query();
        
        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'LIKE', '%' . $searchValue . '%')
                  ->orWhere('surname', 'LIKE', '%' . $searchValue . '%');
            });
        }

        // Hierarchical Data Isolation
        $user = auth()->user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $query->whereHas('user', function($q) use ($deptIds) {
                    $q->whereHas('departments', function($sq) use ($deptIds) {
                        $sq->whereIn('departments.id', $deptIds);
                    });
                });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        $employees = $query->with([
            'employeeCareer' => function ($careerQuery) {
                $careerQuery->orderByDesc('start_date')->orderByDesc('id');
            },
        ])->paginate($perPage, ['*'], 'page', $currentPage);

        $employees->getCollection()->transform(function (Employee $employee) {
            $latestCareer = $employee->employeeCareer->first();

            $birthCertificate = $latestCareer?->getMedia('birth_certificate')->first();
            $nationalCard = $latestCareer?->getMedia('national_card')->first();

            $employee->setAttribute('BC', $birthCertificate?->getUrl());
            $employee->setAttribute('NC', $nationalCard?->getUrl());
            $employee->unsetRelation('employeeCareer');

            return $employee;
        });

        $totalEmployees = $employees->total(); // Total number of users matching the query
        $totalPage = ceil($totalEmployees / $perPage); // Calculate total pages
        $cities = City::all();

        return response()->json(["cities"=> $cities, "employees" => $employees, "totalPage" => $totalPage, "totalEmployees"=>$totalEmployees]);
    }
    public function getEmployee($Id): JsonResponse
    {
       $employee = Employee::find($Id);

        return response()->json(["employee" => $employee]);
    }

    public function store(Request $request) {
        $employee_data = $request->input('employee');
        $employee_data['user_id'] = auth()->id();
        $employee = Employee::create($employee_data);

        return response()->json(['message' => 'Employee created successfully', 'employee' => $employee]);
    }

    public function update(Request $request,$id) {

        $employee_data = $request->input('employee');
        $employee = Employee::find($id);
        $employee->update($employee_data);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Employee updated successfully', 'employee' => $employee]);
    }

    public function destroy(Request $request ,$id){
        $employee = Employee::find($id);
        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully']);

    }

    public function getCities(){
        $cities = City::all();
        return response()->json(['cities' => $cities]);
    }

    /**
     * Import employees from CSV.
     */
    public function importCsv(Request $request): JsonResponse
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
        foreach (['id', 'name', 'surname', 'birthdate', 'birthplace', 'email', 'address', 'phone', 'nin', 'ncn', 'cnas', 'card_issue_date', 'card_issue_place', 'active', 'name_ar', 'surname_ar', 'father_name_ar', 'mother_full_name_ar', 'birth_city_id', 'card_issued_city_id'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;
        $normalizeDate = function ($value) {
            $raw = trim((string) ($value ?? ''));
            if ($raw === '' || strtolower($raw) === 'null') {
                return null;
            }

            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y', 'Y/m/d'];
            foreach ($formats as $format) {
                $dt = \DateTime::createFromFormat($format, $raw);
                if ($dt && $dt->format($format) === $raw) {
                    return $dt->format('Y-m-d');
                }
            }

            $timestamp = strtotime($raw);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }

            return null;
        };

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $activeRaw = strtolower((string) ($rowData['active'] ?? '0'));
            $active = in_array($activeRaw, ['1', 'true', 'yes'], true) ? 1 : 0;
            $normalizedBirthdate = $normalizeDate($rowData['birthdate'] ?? null);
            $normalizedCardIssueDate = $normalizeDate($rowData['card_issue_date'] ?? null);

            if (trim((string) ($rowData['card_issue_date'] ?? '')) !== '' && $normalizedCardIssueDate === null) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'Invalid card_issue_date converted to null.',
                ];
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'name' => $rowData['name'] ?? null,
                'surname' => $rowData['surname'] ?? null,
                'birthdate' => $normalizedBirthdate,
                'birthplace' => $rowData['birthplace'] ?? null,
                'email' => $rowData['email'] ?? null,
                'address' => $rowData['address'] ?? null,
                'phone' => $rowData['phone'] ?? null,
                'NIN' => $rowData['nin'] ?? null,
                'NCN' => $rowData['ncn'] ?? null,
                'CNAS' => $rowData['cnas'] ?? null,
                'card_issue_date' => $normalizedCardIssueDate,
                'card_issue_place' => $rowData['card_issue_place'] ?? null,
                'active' => $active,
                'name_ar' => $rowData['name_ar'] ?? null,
                'surname_ar' => $rowData['surname_ar'] ?? null,
                'father_name_ar' => $rowData['father_name_ar'] ?? null,
                'mother_full_name_ar' => $rowData['mother_full_name_ar'] ?? null,
                'birth_city_id' => isset($rowData['birth_city_id']) && trim((string) $rowData['birth_city_id']) !== '' ? (int) $rowData['birth_city_id'] : null,
                'card_issued_city_id' => isset($rowData['card_issued_city_id']) && trim((string) $rowData['card_issued_city_id']) !== '' ? (int) $rowData['card_issued_city_id'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'birthdate' => 'nullable|date',
                'birthplace' => 'nullable|string|max:255',
                'email' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'NIN' => 'nullable|string|max:255',
                'NCN' => 'nullable|string|max:255',
                'CNAS' => 'nullable|string|max:255',
                'card_issue_date' => 'nullable|date',
                'card_issue_place' => 'nullable|string|max:255',
                'active' => 'required|boolean',
                'name_ar' => 'nullable|string|max:255',
                'surname_ar' => 'nullable|string|max:255',
                'father_name_ar' => 'nullable|string|max:255',
                'mother_full_name_ar' => 'nullable|string|max:255',
                'birth_city_id' => 'nullable|integer',
                'card_issued_city_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            if (!empty($payload['id']) && DB::table('employees')->where('id', $payload['id'])->exists()) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Employee id exists, inserted with auto-generated id.'];
                unset($payload['id']);
            }

            DB::table('employees')->insert($payload);
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

    /**
     * Import employee_careers from CSV.
     */
    public function importCareersCsv(Request $request): JsonResponse
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
        foreach (['id', 'employee_id', 'start_date', 'end_date', 'real_start_date', 'real_end_date', 'position', 'position_ar'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;
        $normalizeDate = function ($value) {
            $raw = trim((string) ($value ?? ''));
            if ($raw === '' || strtolower($raw) === 'null') {
                return null;
            }

            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y', 'Y/m/d'];
            foreach ($formats as $format) {
                $dt = \DateTime::createFromFormat($format, $raw);
                if ($dt && $dt->format($format) === $raw) {
                    return $dt->format('Y-m-d');
                }
            }

            $timestamp = strtotime($raw);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }

            return null;
        };

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $normalizedStartDate = $normalizeDate($rowData['start_date'] ?? null);
            $normalizedEndDate = $normalizeDate($rowData['end_date'] ?? null);
            $normalizedRealStartDate = $normalizeDate($rowData['real_start_date'] ?? null);
            $normalizedRealEndDate = $normalizeDate($rowData['real_end_date'] ?? null);

            if (trim((string) ($rowData['start_date'] ?? '')) !== '' && $normalizedStartDate === null) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Invalid start_date converted to null.'];
            }
            if (trim((string) ($rowData['end_date'] ?? '')) !== '' && $normalizedEndDate === null) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Invalid end_date converted to null.'];
            }
            if (trim((string) ($rowData['real_start_date'] ?? '')) !== '' && $normalizedRealStartDate === null) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Invalid real_start_date converted to null.'];
            }
            if (trim((string) ($rowData['real_end_date'] ?? '')) !== '' && $normalizedRealEndDate === null) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Invalid real_end_date converted to null.'];
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'employee_id' => isset($rowData['employee_id']) ? (int) $rowData['employee_id'] : null,
                'start_date' => $normalizedStartDate,
                'end_date' => $normalizedEndDate,
                'real_start_date' => $normalizedRealStartDate,
                'real_end_date' => $normalizedRealEndDate,
                'position' => $rowData['position'] ?? null,
                'position_ar' => $rowData['position_ar'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'employee_id' => 'required|integer',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'real_start_date' => 'nullable|date',
                'real_end_date' => 'nullable|date',
                'position' => 'nullable|string|max:255',
                'position_ar' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            if (!empty($payload['id']) && DB::table('employee_careers')->where('id', $payload['id'])->exists()) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Employee career id exists, inserted with auto-generated id.'];
                unset($payload['id']);
            }

            DB::table('employee_careers')->insert($payload);
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
