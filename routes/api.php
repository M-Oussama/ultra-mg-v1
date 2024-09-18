<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\CertifyInvoiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientLogController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\VacationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** USERS  */
Route::get('/users/list', [UserController::class, 'getUsers'])->name('getUsers');
Route::post('/users/store', [UserController::class, 'store'])->name('storeUser');
Route::post('/users/update/{id}', [UserController::class, 'update'])->name('updateUser');
Route::delete('/users/delete/{id}', [UserController::class, 'delete'])->name('deleteUser');

/** CLIENTS  */

Route::get('/clients/list', [ClientController::class, 'getClients'])->name('getClients');
Route::post('/clients/store', [ClientController::class, 'store'])->name('store');
Route::post('/clients/update/{id}', [ClientController::class, 'update'])->name('update');
Route::delete('/clients/delete/{id}', [ClientController::class, 'delete'])->name('delete');
Route::get('/clients/getClientsPerCity/{id}', [ClientController::class, 'getClientsPerCity'])->name('getClientsPerCity');

/** PRODUCTS  */

Route::get('/products/list', [ProductController::class, 'getProducts'])->name('getProducts');
Route::post('/products/store', [ProductController::class, 'store'])->name('store');
Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('update');
Route::delete('/products/delete/{id}', [ProductController::class, 'delete'])->name('delete');

/** Certify Invoices */
Route::get('/certifyInvoices/list', [CertifyInvoiceController::class, 'getInvoices'])->name('getInvoices');
Route::get('/certifyInvoices/getInvoice/{id}', [CertifyInvoiceController::class, 'getInvoice'])->name('getInvoice');
Route::post('/certifyInvoices/store', [CertifyInvoiceController::class, 'store'])->name('store');
Route::post('/certifyInvoices/update/{id}', [CertifyInvoiceController::class, 'update'])->name('update');
Route::get('/certifyInvoices/getInvoiceData', [CertifyInvoiceController::class, 'getInvoiceData'])->name('getData');
Route::get('/certifyInvoices/getLastID', [CertifyInvoiceController::class, 'getLastID'])->name('getLastID');

/** POS */
Route::get('/pos/sales/list', [POSController::class, 'getSales'])->name('getSales');
Route::get('/pos/sales/getPriceHistory/{clientId}/{productId}', [POSController::class, 'getPriceHistory'])->name('getPriceHistory');
Route::get('/pos/sales/getData', [POSController::class, 'getData'])->name('getData');
Route::post('/pos/sales/store', [POSController::class, 'store'])->name('store');
Route::post('/pos/sales/delete', [POSController::class, 'deleteSale'])->name('deleteSale');
Route::get('/pos/sale/getSale/{id}', [POSController::class, 'getSale'])->name('getSale');
Route::get('/pos/sale/getSaleData/{id}', [POSController::class, 'getSaleData'])->name('getSale');
Route::post('/pos/sales/update/{id}', [POSController::class, 'update'])->name('update');
Route::post('/pos/sales/payment/create/{id}', [POSController::class, 'addPayment'])->name('addPayment');
Route::get('/pos/sales/payments/list', [POSController::class, 'listPayment'])->name('listPayment');
Route::post('/pos/sales/payment/create', [POSController::class, 'createPayment'])->name('createPayment');
Route::post('/pos/sales/payment/update', [POSController::class, 'updatePayment'])->name('updatePayment');
Route::post('/pos/sales/payment/delete', [POSController::class, 'deletePayment'])->name('deletePayment');
Route::get('/pos/benefits/list', [BenefitController::class, 'getBenefits'])->name('getBenefits');
Route::post('/pos/benefits/store', [BenefitController::class, 'store'])->name('store');
Route::get('/pos/benefits/{id}', [BenefitController::class, 'getArticlesBenefit'])->name('getArticlesBenefit');
Route::delete('/pos/benefits/delete/{id}', [BenefitController::class, 'destroyBenefit'])->name('destroyBenefit');
Route::post('/pos/benefits/update/{id}', [BenefitController::class, 'updateBenefit'])->name('updateBenefit');
Route::post('/pos/benefits/charges/update/{id}', [BenefitController::class, 'updateBenefitCharges'])->name('updateBenefitCharges');
Route::get('/pos/benefits/refresh/{id}', [BenefitController::class, 'refreshBenefitData'])->name('refreshBenefitData');
Route::get('/pos/client/{id}/sales', [POSController::class, 'getClientInvoices'])->name('getClientInvoices');
Route::get('/pos/client/{id}/sales/{paymentId}/paid', [POSController::class, 'getPaidInvoices'])->name('getClientInvoices');

/** EMPLOYEES */
Route::get('/employees/list', [EmployeeController::class, 'getEmployees'])->name('getEmployees');
Route::post('/employees/store', [EmployeeController::class, 'store'])->name('store');
Route::post('/employees/update/{id}', [EmployeeController::class, 'update'])->name('update');
Route::delete('/employees/delete/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
Route::get('/employees/{id}', [EmployeeController::class, 'getEmployee'])->name('getEmployee');

/** Attendances */
Route::get('/attendances/list', [AttendanceController::class, 'getAttendances'])->name('getAttendances');
Route::get('/attendances/getAttendanceData/{id}', [AttendanceController::class, 'getAttendanceData'])->name('getAttendanceData');
Route::post('/attendances/store', [AttendanceController::class, 'store'])->name('store');
Route::post('/attendances/submit', [AttendanceController::class, 'submit'])->name('submit');
Route::get('/attendances/{id}', [AttendanceController::class, 'getAttendance'])->name('getAttendance');
Route::get('/attendances/getAttendanceByID/{id}', [AttendanceController::class, 'getAttendanceByID'])->name('getAttendanceByID');
Route::get('/attendances/edit/{id}', [AttendanceController::class, 'getAttendance'])->name('getAttendance');
Route::post('/attendances/update', [AttendanceController::class, 'update'])->name('update');
Route::post('/attendances/AddEmployeeToAttendance', [AttendanceController::class, 'AddEmployeeToAttendance'])->name('AddEmployeeToAttendance');
Route::post('/attendances/RemoveEmployeeFromAttendance', [AttendanceController::class, 'RemoveEmployeeFromAttendance'])->name('RemoveEmployeeFromAttendance');
Route::get('attendances/employees/list/{id}', [AttendanceController::class, 'fetchEmployeesByAttendance'])->name('fetchEmployeesByAttendance');
Route::post('attendances/updateEndDate/{id}', [AttendanceController::class, 'updateEndDate'])->name('updateEndDate');
Route::post('attendances/addNewEmployeeAttendanceRecord/{id}', [AttendanceController::class, 'NewEmployeeAttendanceRecord'])->name('NewEmployeeAttendanceRecord');
Route::get('/attendances/career/delete/{id}', [AttendanceController::class, 'deleteEmployeeCareer'])->name('deleteEmployeeCareer');

/**
 * logs
 */
Route::post('/clients/log', [ClientLogController::class, 'getLog'])->name('getLog');
Route::post('/clients/log/generate', [ClientLogController::class, 'getALLLog'])->name('getLog');

/**
 *  supplier
 */


    Route::group(['prefix' => 'suppliers'], function () {
        Route::post('/list', [SupplierController::class, 'getSuppliers']);
        Route::post('/create', [SupplierController::class, 'create']);
        Route::post('/update', [SupplierController::class, 'update']);
        Route::post('/delete', [SupplierController::class, 'delete']);
    });

    Route::group(['prefix' => 'returns'], function () {
        Route::get('/list', [ProductReturnController::class, 'getReturns']);
        Route::post('/store', [ProductReturnController::class, 'store']);
        Route::get('/getData', [ProductReturnController::class, 'getData']);
        Route::post('/delete', [ProductReturnController::class, 'deleteReturn']);
        Route::post('/update/{id}', [ProductReturnController::class, 'update']);
        Route::get('/getReturnData/{id}', [ProductReturnController::class, 'getReturnData']);
        Route::get('/getReturn/{id}', [ProductReturnController::class, 'getReturn']);
});
    Route::group(['prefix' => '/employees/vacation'], function () {
        Route::post('/store/{id}', [VacationController::class, 'store']);
        Route::post('/update/{id}', [VacationController::class, 'update']);
        Route::get('/list/{id}', [VacationController::class, 'getVacationsByEmployee']);
        Route::get('/list', [VacationController::class, 'getVacations']);
        Route::delete('/delete/{id}', [VacationController::class, 'destroy']);
        Route::get('/{id}', [VacationController::class, 'getVacation']);
    });
/**
 * Recruitment
 */
Route::post('/recrutement/generateEmail/{id}', [AttendanceController::class, 'generateEmail'])->name('generateEmail');
Route::get('/client-log/{id}/download', [ClientController::class, 'exportClientLog'])->name('exportClientLog');
Route::get('/client-log/return/{id}/download', [ClientController::class, 'exportClientLogWithReturn'])->name('exportClientLogWithReturn');


Route::group(['prefix' => '/dashboard'], function () {
    Route::get('/vacations', [DashboardController::class, 'getEmployeeInVacation']);
    Route::get('/incoming-vacations', [DashboardController::class, 'getIncomingVacations'])->name('incoming-vacations');
    Route::get('/maintenances/incoming', [DashboardController::class, 'getIncoming']);
    Route::get('/maintenances/recent', [DashboardController::class, 'getRecent']);
    Route::get('/maintenances', [DashboardController::class, 'getMaintenance']);
    Route::get('/admin', [DashboardController::class, 'getAdminDashboard']);

});
Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'Login']);

})->middleware('auth:sanctum');

Route::group(['prefix' => '/roles'], function () {
    Route::get('/list', [RoleController::class, 'list']);
    Route::post('/store', [RoleController::class, 'store']);
    Route::post('/update/{id}', [RoleController::class, 'update']);
    Route::delete('/delete/{id}', [RoleController::class, 'delete']);


});
Route::group(['prefix' => '/permissions'], function () {
    Route::get('/list', [PermissionController::class, 'list']);
    Route::post('/store', [PermissionController::class, 'store']);
    Route::post('/update/{id}', [PermissionController::class, 'update']);
    Route::delete('/delete/{id}', [PermissionController::class, 'delete']);
});

Route::group(['prefix' => '/assets'], function () {
    Route::get('/list', [AssetController::class, 'list']);
    Route::get('/{id}/components', [AssetController::class, 'getComponents']);
    Route::post('/store', [AssetController::class, 'store']);
    Route::post('/update/{id}', [AssetController::class, 'update']);
    Route::delete('/delete/{id}', [AssetController::class, 'delete']);
});
Route::group(['prefix' => '/components'], function () {
    Route::get('/list', [ComponentController::class, 'list']);
    Route::post('/store', [ComponentController::class, 'store']);
    Route::post('/update/{id}', [ComponentController::class, 'update']);
    Route::delete('/delete/{id}', [ComponentController::class, 'delete']);
});
Route::group(['prefix' => '/maintenances'], function () {
    Route::get('/list', [MaintenanceController::class, 'list']);
    Route::post('/store', [MaintenanceController::class, 'store']);
    Route::post('/update/{id}', [MaintenanceController::class, 'update']);
    Route::delete('/delete/{id}', [MaintenanceController::class, 'delete']);

    Route::get('/recent', [MaintenanceController::class, 'recent']);

})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
