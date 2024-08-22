<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\YearlyVacation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class DashboardController extends Controller
{
    public function getEmployeeInVacation(Request $request): \Illuminate\Http\JsonResponse
    {

        $perPage = $request->input('perPage', 5); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $vacations = YearlyVacation::with('employee')->where('end_date', '>=', date("Y/m/d"));

        $vacationsAll = $vacations->get();
        $vacationsPage = $vacations->paginate($perPage, ['*'], 'page', $currentPage);

        $totalVacations = $vacationsPage->total(); // Total number of objects matching the query
        $totalPage = ceil($totalVacations / $perPage); // Calculate total pages

        return response()->json(['vacations' => $vacationsPage, 'vacationsAll' => $vacationsAll, "totalPage" => $totalPage, "totalVacations"=>$totalVacations,]);
    }

    public function getIncomingVacations(Request $request): \Illuminate\Http\JsonResponse
    {

        $perPage = $request->input('perPage', 5); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $tenMonthsAgo = Carbon::today()->subMonths(10);
        $elevenMonthsAgo = Carbon::today()->subMonths(12);

        $vacations = YearlyVacation::with('employee')->where('end_date', '<', Carbon::today())
            ->where('end_date', '<=', $tenMonthsAgo)
            ->where('end_date', '>=', $elevenMonthsAgo);

        $vacationsAll = $vacations->get();
        $vacationsPage = $vacations->paginate($perPage, ['*'], 'page', $currentPage);

        $totalVacations = $vacationsPage->total(); // Total number of objects matching the query
        $totalPage = ceil($totalVacations / $perPage); // Calculate total pages

        return response()->json(['vacations' => $vacationsPage, 'vacationsAll' => $vacationsAll, "totalPage" => $totalPage, "totalVacations"=>$totalVacations,]);
    }

    public function getIncoming(Request $request): JsonResponse
    {

        $perPage = $request->input('perPage', 5); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $maintenances = Maintenance::orderBy('next_maintenance_date', 'asc')->where('next_maintenance_date', '>=' , Carbon::today());

        $maintenancesAll = $maintenances->get();
        $maintenancesPage = $maintenances->paginate($perPage, ['*'], 'page', $currentPage);

        $totalMaintenances = $maintenancesPage->total(); // Total number of objects matching the query
        $totalPage = ceil($totalMaintenances / $perPage); // Calculate total pages

        return response()->json(['maintenances' => $maintenancesAll, 'maintenancesAll' => $maintenancesAll, "totalPage" => $totalPage, "totalMaintenances"=>$totalMaintenances,]);
    }

    public function getRecent(Request $request): JsonResponse
    {

        $perPage = $request->input('perPage', 5); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $maintenances = Maintenance::orderBy('next_maintenance_date', 'asc')->where('maintenance_date', '<=' , Carbon::today())->where('status', 'Done');

        $maintenancesAll = $maintenances->get();
        $maintenancesPage = $maintenances->paginate($perPage, ['*'], 'page', $currentPage);

        $totalMaintenances = $maintenancesPage->total(); // Total number of objects matching the query
        $totalPage = ceil($totalMaintenances / $perPage); // Calculate total pages

        return response()->json(['maintenances' => $maintenancesAll, 'maintenancesAll' => $maintenancesAll, "totalPage" => $totalPage, "totalMaintenances"=>$totalMaintenances,]);
    }

    public function getMaintenance(Request $request): JsonResponse
    {

        $perPage = $request->input('perPage', 5); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $maintenances = Maintenance::orderBy('next_maintenance_date', 'asc')->where('maintenance_date', '<=' , Carbon::today())->where('status', 'Done');

        $maintenancesAll = $maintenances->get();


        $incomingMaintenance = Maintenance::orderBy('next_maintenance_date', 'asc')->where('next_maintenance_date', '>=' , Carbon::today());

        $incomingMaintenances = $incomingMaintenance->get();


        return response()->json(['maintenances' => $maintenancesAll, "incomingMaintenances" => $incomingMaintenances]);
    }

    public function getAdminDashboard(Request $request): JsonResponse
    {

        $perPage = $request->input('perPage', 5); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $vacation = YearlyVacation::with('employee')->where('end_date', '>=', date("Y/m/d"));

        $vacations = $vacation->get();


        $tenMonthsAgo = Carbon::today()->subMonths(10);
        $elevenMonthsAgo = Carbon::today()->subMonths(12);

        $IncomingVacation = YearlyVacation::with('employee')->where('end_date', '<', Carbon::today())
            ->where('end_date', '<=', $tenMonthsAgo)
            ->where('end_date', '>=', $elevenMonthsAgo);

        $IncomingVacations = $IncomingVacation->get();


        return response()->json(['vacationList' => $vacations, "incomingVacationList" => $IncomingVacations]);
    }



}
