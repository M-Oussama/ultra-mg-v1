<?php

namespace App\Http\Controllers;

use App\Models\YearlyVacation;
use Carbon\Carbon;
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
}
