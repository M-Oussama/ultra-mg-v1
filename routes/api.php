<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\CertifyInvoiceController;
use App\Http\Controllers\CertifyProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientLogController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ImportationInvoiceController;
use App\Http\Controllers\ImportationPaymentController;
use App\Http\Controllers\SalesSupplierController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\ZKAssignmentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\RealLogisticsInvoiceController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\TransactionController;
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

Route::post('/auth/login', [AuthController::class, 'Login']);

// THE ULTIMATE FIX: Force rename the column via direct SQL
Route::get('/force-fix-tokens', function() {
    try {
        if (\Illuminate\Support\Facades\Schema::hasColumn('personal_access_tokens', 'tokenable')) {
            \Illuminate\Support\Facades\DB::statement(
                'ALTER TABLE personal_access_tokens CHANGE `tokenable` `tokenable_legacy` VARCHAR(255) NULL DEFAULT NULL'
            );
            return response()->json(['success' => true, 'message' => 'Column successfully renamed to tokenable_legacy. Sanctum should now work!']);
        }
        return response()->json(['success' => true, 'message' => 'Column tokenable was already renamed or does not exist.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

/** DEBUG AUTH */
Route::get('/test-auth', function (Request $request) {
    return response()->json([
        'user' => $request->user('sanctum'),
        'authenticated' => (bool) $request->user('sanctum'),
        'header' => $request->header('Authorization'),
    ]);
});

Route::get('/deep-debug-auth', function (Request $request) {
    $header = $request->header('Authorization');
    $tokenStr = str_replace('Bearer ', '', $header);
    
    if (strpos($tokenStr, '|') !== false) {
        [$id, $plainToken] = explode('|', $tokenStr, 2);
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::find($id);
        
        if ($tokenModel) {
            $isValid = hash_equals($tokenModel->token, hash('sha256', $plainToken));
            return response()->json([
                'token_found' => true,
                'id_match' => $id,
                'hash_valid' => $isValid,
                'tokenable_type' => $tokenModel->tokenable_type,
                'tokenable_id' => $tokenModel->tokenable_id,
                'tokenable_resolved_type' => gettype($tokenModel->tokenable),
                'tokenable_is_user' => $tokenModel->tokenable instanceof \App\Models\User,
                'user_name' => $tokenModel->tokenable ? $tokenModel->tokenable->name : 'N/A',
                'raw_token_data' => $tokenModel->toArray(),
            ]);
        }
    }
    
    return response()->json(['error' => 'Token not found or invalid format', 'header' => $header]);
});

Route::get('/check-db-schema', function() {
    try {
        $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE personal_access_tokens');
        return response()->json([
            'success' => true,
            'columns' => $columns
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

Route::middleware('auth:sanctum')->group(function () {
    /** USERS  */
    Route::get('/users/list', [UserController::class, 'getUsers'])->middleware('permission:list,users')->name('getUsers');
    Route::post('/users/store', [UserController::class, 'store'])->middleware('permission:add,users')->name('storeUser');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->middleware('permission:edit,users')->name('updateUser');
    Route::delete('/users/delete/{id}', [UserController::class, 'delete'])->middleware('permission:delete,users')->name('deleteUser');

    /** CLIENTS  */
    Route::get('/clients/list', [ClientController::class, 'getClients'])->middleware('permission:list,clients')->name('getClients');
    Route::get('/clients/all', [ClientController::class, 'getAllClients'])->middleware('permission:list,clients')->name('getAllClients');
    Route::get('/clients/cities', [ClientController::class, 'getCities'])->middleware('permission:list,clients')->name('getClientCities');
    Route::post('/clients/store', [ClientController::class, 'store'])->middleware('permission:add,clients')->name('store');
    Route::post('/clients/import-csv', [ClientController::class, 'importCsv'])->middleware('permission:add,clients')->name('importClientsCsv');
    Route::post('/clients/update/{id}', [ClientController::class, 'update'])->middleware('permission:edit,clients')->name('update');
    Route::delete('/clients/delete/{id}', [ClientController::class, 'delete'])->middleware('permission:delete,clients')->name('delete');
    Route::get('/clients/getClientsPerCity/{id}', [ClientController::class, 'getClientsPerCity'])->middleware('permission:list,clients')->name('getClientsPerCity');

    /** PRODUCTS  */
    Route::get('/products/list', [ProductController::class, 'getProducts'])->middleware('permission:list,products')->name('getProducts');
    Route::get('/products/get/{id}', [ProductController::class, 'getProduct'])->middleware('permission:list,products')->name('getProduct');
    Route::post('/products/store', [ProductController::class, 'store'])->middleware('permission:add,products')->name('store');
    Route::post('/products/import-csv', [ProductController::class, 'importCsv'])->middleware('permission:add,products')->name('importProductsCsv');
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->middleware('permission:edit,products')->name('update');
    Route::delete('/products/delete/{id}', [ProductController::class, 'delete'])->middleware('permission:delete,products')->name('delete');

    /** Certify Invoices */
    Route::get('/certifyInvoices/list', [CertifyInvoiceController::class, 'getInvoices'])->middleware('permission:list,certify_invoices')->name('getInvoices');
    Route::get('/certifyInvoices/getInvoice/{id}', [CertifyInvoiceController::class, 'getInvoice'])->middleware('permission:list,certify_invoices')->name('getInvoice');
    Route::post('/certifyInvoices/store', [CertifyInvoiceController::class, 'store'])->middleware('permission:add,certify_invoices')->name('store');
    Route::post('/certifyInvoices/import-csv', [CertifyInvoiceController::class, 'importCsv'])->middleware('permission:add,certify_invoices')->name('importCertifyInvoiceCsv');
    Route::post('/certifyInvoices/import-products-csv', [CertifyInvoiceController::class, 'importProductsCsv'])->middleware('permission:add,certify_invoices')->name('importCertifyInvoiceProductsCsv');
    Route::post('/certifyInvoices/update/{id}', [CertifyInvoiceController::class, 'update'])->middleware('permission:edit,certify_invoices')->name('update');
    Route::delete('/certifyInvoices/delete/{id}', [CertifyInvoiceController::class, 'delete'])->middleware('permission:delete,certify_invoices')->name('deleteCertifyInvoice');
    Route::get('/certifyInvoices/getInvoiceData', [CertifyInvoiceController::class, 'getData'])->middleware('permission:list,certify_invoices')->name('getData');
    Route::get('/certifyInvoices/getLastID', [CertifyInvoiceController::class, 'getLastID'])->middleware('permission:list,certify_invoices')->name('getLastID');

    /** Certify Clients */
    Route::get('/certify-clients/list', [\App\Http\Controllers\CertifyClientController::class, 'getClients'])->middleware('permission:list,certify_invoices')->name('getCertifyClients');
    Route::post('/certify-clients/store', [\App\Http\Controllers\CertifyClientController::class, 'store'])->middleware('permission:add,certify_invoices')->name('storeCertifyClient');
    Route::post('/certify-clients/import-csv', [\App\Http\Controllers\CertifyClientController::class, 'importCsv'])->middleware('permission:add,certify_invoices')->name('importCertifyClientCsv');
    Route::post('/certify-clients/update/{id}', [\App\Http\Controllers\CertifyClientController::class, 'update'])->middleware('permission:edit,certify_invoices')->name('updateCertifyClient');
    Route::delete('/certify-clients/delete/{id}', [\App\Http\Controllers\CertifyClientController::class, 'delete'])->middleware('permission:delete,certify_invoices')->name('deleteCertifyClient');

    /** Certify Products */
    Route::get('/certify-products/list', [CertifyProductController::class, 'getProducts'])->middleware('permission:list,certify_invoices')->name('getCertifyProducts');
    Route::post('/certify-products/store', [CertifyProductController::class, 'store'])->middleware('permission:add,certify_invoices')->name('storeCertifyProduct');
    Route::post('/certify-products/import-csv', [CertifyProductController::class, 'importCsv'])->middleware('permission:add,certify_invoices')->name('importCertifyProductCsv');
    Route::post('/certify-products/update/{id}', [CertifyProductController::class, 'update'])->middleware('permission:edit,certify_invoices')->name('updateCertifyProduct');
    Route::delete('/certify-products/delete/{id}', [CertifyProductController::class, 'delete'])->middleware('permission:delete,certify_invoices')->name('deleteCertifyProduct');

    /** Cheques */
    Route::get('/cheques/list', [\App\Http\Controllers\ChequeController::class, 'getCheques'])->middleware('permission:list,payments')->name('getCheques');
    Route::get('/cheques/available', [\App\Http\Controllers\ChequeController::class, 'getAvailable'])->middleware('permission:list,payments')->name('getAvailableCheques');
    Route::get('/cheques/status/{chequeId}/{excludeCommandId}', [\App\Http\Controllers\ChequeController::class, 'getStatusExcluding'])->middleware('permission:list,payments')->name('getChequeStatusExcluding');
    Route::get('/cheques/status/{chequeId}', [\App\Http\Controllers\ChequeController::class, 'getStatus'])->middleware('permission:list,payments')->name('getChequeStatus');
    Route::post('/cheques/store', [\App\Http\Controllers\ChequeController::class, 'store'])->middleware('permission:add,payments')->name('storeCheque');
    Route::post('/cheques/import-csv', [\App\Http\Controllers\ChequeController::class, 'importCsv'])->middleware('permission:add,payments')->name('importChequeCsv');
    Route::post('/cheques/update/{id}', [\App\Http\Controllers\ChequeController::class, 'update'])->middleware('permission:edit,payments')->name('updateCheque');
    Route::post('/cheques/scan', [\App\Http\Controllers\ChequeController::class, 'scan'])->middleware('permission:add,payments')->name('scanCheque');
    Route::get('/cheques/preview-file', [\App\Http\Controllers\ChequeController::class, 'previewFile'])->middleware('permission:list,payments')->name('previewChequeFile');
    Route::get('/cheques/unify-status', [\App\Http\Controllers\ChequeController::class, 'unifyStatuses'])->middleware('permission:edit,payments')->name('unifyChequeStatuses');
    Route::delete('/cheques/delete/{id}', [\App\Http\Controllers\ChequeController::class, 'delete'])->middleware('permission:delete,payments')->name('deleteCheque');

    /** POS */
    Route::get('/pos/sales/list', [POSController::class, 'getSales'])->middleware('permission:list,sales')->name('getSales');
    Route::get('/pos/sales/getPriceHistory/{clientId}/{productId}', [POSController::class, 'getPriceHistory'])->middleware('permission:list,sales')->name('getPriceHistory');
    Route::get('/pos/sales/getData', [POSController::class, 'getData'])->middleware('permission:list,sales')->name('getData');
    Route::post('/pos/sales/store', [POSController::class, 'store'])->middleware('permission:add,sales')->name('store');
    Route::post('/pos/sales/import-csv', [POSController::class, 'importCsv'])->middleware('permission:add,sales')->name('importSalesCsv');
    Route::post('/pos/sales/import-items-csv', [POSController::class, 'importSaleItemsCsv'])->middleware('permission:add,sales')->name('importSaleItemsCsv');
    Route::post('/pos/sales/delete', [POSController::class, 'deleteSale'])->middleware('permission:delete,sales')->name('deleteSale');
    Route::get('/pos/sale/getSale/{id}', [POSController::class, 'getSale'])->middleware('permission:list,sales')->name('getSale');
    Route::get('/pos/sale/getSaleData/{id}', [POSController::class, 'getSaleData'])->middleware('permission:list,sales')->name('getSale');
    Route::post('/pos/sales/update/{id}', [POSController::class, 'update'])->middleware('permission:edit,sales')->name('update');
    Route::post('/pos/sales/payment/create/{id}', [POSController::class, 'addPayment'])->middleware('permission:add,payments')->name('addPayment');
    Route::get('/pos/sales/payments/list', [POSController::class, 'listPayment'])->middleware('permission:list,payments')->name('listPayment');
    Route::post('/pos/payments/import-csv', [POSController::class, 'importPaymentsCsv'])->middleware('permission:add,payments')->name('importPaymentsCsv');
    Route::post('/pos/partial-payments/import-csv', [POSController::class, 'importPartialPaymentsCsv'])->middleware('permission:add,payments')->name('importPartialPaymentsCsv');
    Route::get('/pos/sales/payments/invoice/{sale_id}', [POSController::class, 'getSalePaymentsTotal'])->middleware('permission:list,payments');
    Route::post('/pos/sales/payment/create', [POSController::class, 'createPayment'])->middleware('permission:add,payments')->name('createPayment');
    Route::post('/pos/sales/payment/update', [POSController::class, 'updatePayment'])->middleware('permission:edit,payments')->name('updatePayment');
    Route::post('/pos/sales/payment/delete', [POSController::class, 'deletePayment'])->middleware('permission:delete,payments')->name('deletePayment');
    Route::post('/pos/stocks/recalculate', [POSController::class, 'recalculateStocks'])->middleware('permission:admin,dashboard')->name('recalculateStocks');
    Route::get('/pos/benefits/list', [BenefitController::class, 'getBenefits'])->middleware('permission:list,benefits')->name('getBenefits');
    Route::post('/pos/benefits/store', [BenefitController::class, 'store'])->middleware('permission:add,benefits')->name('store');
    Route::get('/pos/benefits/{id}', [BenefitController::class, 'getArticlesBenefit'])->middleware('permission:list,benefits')->name('getArticlesBenefit');
    Route::delete('/pos/benefits/delete/{id}', [BenefitController::class, 'destroyBenefit'])->middleware('permission:delete,benefits')->name('destroyBenefit');
    Route::post('/pos/benefits/update/{id}', [BenefitController::class, 'updateBenefit'])->middleware('permission:edit,benefits')->name('updateBenefit');
    Route::post('/pos/benefits/charges/update/{id}', [BenefitController::class, 'updateBenefitCharges'])->middleware('permission:edit,benefits')->name('updateBenefitCharges');
    Route::get('/pos/benefits/refresh/{id}', [BenefitController::class, 'refreshBenefitData'])->middleware('permission:list,benefits')->name('refreshBenefitData');
    Route::get('/pos/client/{id}/sales', [POSController::class, 'getClientInvoices'])->middleware('permission:list,sales')->name('getClientInvoices');
    Route::get('/pos/client/{id}/sales/{paymentId}/paid', [POSController::class, 'getPaidInvoices'])->middleware('permission:list,sales')->name('getClientInvoices');
    Route::post('/sales/{sale}/toggle-pickup', [POSController::class, 'updatePickUp'])->middleware('permission:edit,sales')->name('updatePickUp');
    Route::get('/pdf/sale/{id}', [PDFController::class, 'exportSaleDeliveryNote'])->middleware('permission:list,sales')->name('exportSaleDeliveryNote');
    Route::get('/pdf/sale-delivery/{id}', [PDFController::class, 'exportSaleDeliveryNote'])->middleware('permission:list,sales')->name('exportSaleDeliveryPdf');
    Route::get('/pdf/sale-preparation/{id}', [PDFController::class, 'exportSalePreparationNote'])->middleware('permission:list,sales')->name('exportSalePreparationNote');
    Route::get('/pdf/certify-invoice/{id}', [PDFController::class, 'exportCertifyInvoice'])->middleware('permission:list,certify_invoices')->name('exportCertifyInvoice');
    Route::get('/pdf/multi-certify-invoices', [PDFController::class, 'exportMultiCertifyInvoices'])->middleware('permission:list,certify_invoices')->name('exportMultiCertifyInvoices');
    Route::get('/pdf/products/list', [PDFController::class, 'exportProductsList'])->middleware('permission:list,products')->name('exportProductsList');
    Route::get('/pdf/clients/list', [PDFController::class, 'exportClientsList'])->middleware('permission:list,clients')->name('exportClientsList');
    Route::get('/pdf/suppliers/list', [PDFController::class, 'exportSuppliersList'])->middleware('permission:list,suppliers')->name('exportSuppliersList');
    Route::get('/pdf/sales-suppliers/list', [PDFController::class, 'exportSalesSuppliersList'])->middleware('permission:list,suppliers')->name('exportSalesSuppliersList');
    Route::get('/pdf/employees/list', [PDFController::class, 'exportEmployeesList'])->middleware('permission:list,employees')->name('exportEmployeesList');
    Route::get('/pdf/attendances/list', [PDFController::class, 'exportAttendancesList'])->middleware('permission:list,attendances')->name('exportAttendancesList');
    Route::get('/pdf/attendance-plans/monthly-work-days', [PDFController::class, 'exportMonthlyWorkDays'])->middleware('permission:list,attendances')->name('exportMonthlyWorkDays');
    Route::get('/pdf/stock/report', [PDFController::class, 'exportStockReport'])->middleware('permission:list,sales')->name('exportStockReport');

    /** EMPLOYEES */
    Route::get('/employees/list', [EmployeeController::class, 'getEmployees'])->middleware('permission:list,employees')->name('getEmployees');
    Route::post('/employees/store', [EmployeeController::class, 'store'])->middleware('permission:add,employees')->name('store');
    Route::post('/employees/import-csv', [EmployeeController::class, 'importCsv'])->middleware('permission:add,employees')->name('importEmployeesCsv');
    Route::post('/employees/import-careers-csv', [EmployeeController::class, 'importCareersCsv'])->middleware('permission:add,employees')->name('importEmployeeCareersCsv');
    Route::post('/employees/update/{id}', [EmployeeController::class, 'update'])->middleware('permission:edit,employees')->name('update');
    Route::delete('/employees/delete/{id}', [EmployeeController::class, 'destroy'])->middleware('permission:delete,employees')->name('destroy');
    Route::get('/employees/{id}', [EmployeeController::class, 'getEmployee'])->middleware('permission:list,employees')->name('getEmployee');
    Route::get('/cities/list', [EmployeeController::class, 'getCities'])->middleware('permission:list,employees')->name('getCities');

    /** Attendances */
    Route::get('/attendances/list', [AttendanceController::class, 'getAttendances'])->middleware('permission:list,attendances')->name('getAttendances');
    Route::get('/attendances/getAttendanceData/{id}', [AttendanceController::class, 'getAttendanceData'])->middleware('permission:view,attendances')->name('getAttendanceData');
    Route::post('/attendances/store', [AttendanceController::class, 'store'])->middleware('permission:add,attendances')->name('store');
    Route::post('/attendances/submit', [AttendanceController::class, 'submit'])->middleware('permission:add,attendances')->name('submit');
    Route::get('/attendances/{id}', [AttendanceController::class, 'getAttendance'])->middleware('permission:view,attendances')->name('getAttendance');
    Route::get('/attendances/getAttendanceByID/{id}', [AttendanceController::class, 'getAttendanceByID'])->middleware('permission:view,attendances')->name('getAttendanceByID');
    Route::get('/attendances/edit/{id}', [AttendanceController::class, 'getAttendance'])->middleware('permission:edit,attendances')->name('getAttendance');
    Route::post('/attendances/update', [AttendanceController::class, 'update'])->middleware('permission:edit,attendances')->name('update');
    Route::post('/attendances/AddEmployeeToAttendance', [AttendanceController::class, 'AddEmployeeToAttendance'])->middleware('permission:edit,attendances')->name('AddEmployeeToAttendance');
    Route::post('/attendances/RemoveEmployeeFromAttendance', [AttendanceController::class, 'RemoveEmployeeFromAttendance'])->middleware('permission:edit,attendances')->name('RemoveEmployeeFromAttendance');
    Route::get('attendances/employees/list/{id}', [AttendanceController::class, 'fetchEmployeesByAttendance'])->middleware('permission:list,attendances')->name('fetchEmployeesByAttendance');
    Route::post('attendances/updateEndDate/{id}', [AttendanceController::class, 'updateEndDate'])->middleware('permission:edit,attendances')->name('updateEndDate');
    Route::post('attendances/addNewEmployeeAttendanceRecord/{id}', [AttendanceController::class, 'NewEmployeeAttendanceRecord'])->middleware('permission:edit,attendances')->name('NewEmployeeAttendanceRecord');
    Route::get('/attendances/career/delete/{id}', [AttendanceController::class, 'deleteEmployeeCareer'])->middleware('permission:delete,attendances')->name('deleteEmployeeCareer');

    /** Attendance planning */
    Route::get('/attendance-plans/active-employees', [\App\Http\Controllers\AttendancePlanningController::class, 'getActiveEmployees'])->middleware('permission:list,attendances')->name('getAttendanceActiveEmployees');
    Route::post('/attendance-plans/active-employees', [\App\Http\Controllers\AttendancePlanningController::class, 'saveActiveEmployees'])->middleware('permission:edit,attendances')->name('saveAttendanceActiveEmployees');
    Route::get('/attendance-plans/monthly-work-days', [\App\Http\Controllers\AttendancePlanningController::class, 'getMonthlyWorkDays'])->middleware('permission:list,attendances')->name('getAttendanceMonthlyWorkDays');
    Route::post('/attendance-plans/monthly-work-days', [\App\Http\Controllers\AttendancePlanningController::class, 'saveMonthlyWorkDays'])->middleware('permission:edit,attendances')->name('saveAttendanceMonthlyWorkDays');

    /** LOGS */
    Route::post('/clients/log', [ClientLogController::class, 'getLog'])->name('getLog');
    Route::post('/clients/log/generate', [ClientLogController::class, 'getALLLog'])->name('getLog');

    /** DEPARTMENTS */
    Route::group(['prefix' => 'departments'], function () {
        Route::get('/index', [DepartmentController::class, 'index'])->middleware('permission:list,departments');
        Route::get('/list', [DepartmentController::class, 'list'])->middleware('permission:list,departments');
        Route::post('/create', [DepartmentController::class, 'create'])->middleware('permission:add,departments');
        Route::post('/update', [DepartmentController::class, 'update'])->middleware('permission:edit,departments');
        Route::post('/delete', [DepartmentController::class, 'delete'])->middleware('permission:delete,departments');
    });
});

/**
 *  supplier
 */


Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'suppliers'], function () {
        Route::post('/list', [SupplierController::class, 'getSuppliers'])->middleware('permission:list,suppliers');
        Route::post('/create', [SupplierController::class, 'create'])->middleware('permission:add,suppliers');
        Route::post('/update', [SupplierController::class, 'update'])->middleware('permission:edit,suppliers');
        Route::post('/delete', [SupplierController::class, 'delete'])->middleware('permission:delete,suppliers');
    });

    Route::group(['prefix' => 'sales-suppliers'], function () {
        Route::get('/list', [SalesSupplierController::class, 'getSuppliers'])->middleware('permission:list,suppliers');
        Route::get('/getData', [SalesSupplierController::class, 'getData'])->middleware('permission:list,suppliers');
        Route::post('/create', [SalesSupplierController::class, 'create'])->middleware('permission:add,suppliers');
        Route::post('/update/{id}', [SalesSupplierController::class, 'update'])->middleware('permission:edit,suppliers');
        Route::post('/delete', [SalesSupplierController::class, 'delete'])->middleware('permission:delete,suppliers');
    });

    Route::group(['prefix' => 'supplies'], function () {
        Route::get('/list', [SupplyController::class, 'getSupplies'])->middleware('permission:list,suppliers');
        Route::get('/getData', [SupplyController::class, 'getData'])->middleware('permission:list,suppliers');
        Route::get('/stock-batches', [SupplyController::class, 'getStockBatches'])->middleware('permission:list,suppliers');
        Route::post('/store', [SupplyController::class, 'store'])->middleware('permission:add,suppliers');
        Route::post('/update/{id}', [SupplyController::class, 'update'])->middleware('permission:edit,suppliers');
        Route::get('/show/{id}', [SupplyController::class, 'show'])->middleware('permission:list,suppliers');
        Route::post('/delete', [SupplyController::class, 'delete'])->middleware('permission:delete,suppliers');
    });

    Route::group(['prefix' => 'importation-invoices'], function () {
        Route::get('/list', [ImportationInvoiceController::class, 'list'])->middleware('permission:list,importations');
        Route::post('/store', [ImportationInvoiceController::class, 'store'])->middleware('permission:add,importations');
        Route::post('/update/{id}', [ImportationInvoiceController::class, 'update'])->middleware('permission:edit,importations');
        Route::delete('/delete/{id}', [ImportationInvoiceController::class, 'delete'])->middleware('permission:delete,importations');
    });

    Route::group(['prefix' => 'importation-payments'], function () {
        Route::get('/list/{invoice_id?}', [ImportationPaymentController::class, 'list'])->middleware('permission:list,importations');
        Route::post('/store', [ImportationPaymentController::class, 'store'])->middleware('permission:add,importations');
        Route::post('/update/{id}', [ImportationPaymentController::class, 'update'])->middleware('permission:edit,importations');
        Route::delete('/delete/{id}', [ImportationPaymentController::class, 'delete'])->middleware('permission:delete,importations');
    });

    Route::group(['prefix' => 'sub-certify-invoices'], function () {
        Route::get('/list', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'getInvoices'])->middleware('permission:list,certify_invoices');
        Route::get('/getInvoice/{id}', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'getInvoice'])->middleware('permission:list,certify_invoices');
        Route::post('/store', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'store'])->middleware('permission:add,certify_invoices');
        Route::post('/update/{id}', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'update'])->middleware('permission:edit,certify_invoices');
        Route::delete('/delete/{id}', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'delete'])->middleware('permission:delete,certify_invoices');
        Route::get('/getInvoiceData', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'getInvoiceData'])->middleware('permission:list,certify_invoices');
        Route::get('/getLastID', [\App\Http\Controllers\SubCertifyInvoiceController::class, 'getLastID'])->middleware('permission:list,certify_invoices');
    });

    Route::group(['prefix' => 'returns'], function () {
        Route::get('/list', [ProductReturnController::class, 'getReturns'])->middleware('permission:list,returns');
        Route::post('/store', [ProductReturnController::class, 'store'])->middleware('permission:add,returns');
        Route::post('/import-csv', [ProductReturnController::class, 'importReturnsCsv'])->middleware('permission:add,returns');
        Route::post('/import-lists-csv', [ProductReturnController::class, 'importReturnListsCsv'])->middleware('permission:add,returns');
        Route::get('/getData', [ProductReturnController::class, 'getData'])->middleware('permission:list,returns');
        Route::post('/delete', [ProductReturnController::class, 'deleteReturn'])->middleware('permission:delete,returns');
        Route::post('/update/{id}', [ProductReturnController::class, 'update'])->middleware('permission:edit,returns');
        Route::get('/getReturnData/{id}', [ProductReturnController::class, 'getReturnData'])->middleware('permission:list,returns');
        Route::get('/getReturn/{id}', [ProductReturnController::class, 'getReturn'])->middleware('permission:list,returns');
    });

    Route::group(['prefix' => '/employees/vacation'], function () {
        Route::post('/store/{id}', [VacationController::class, 'store'])->middleware('permission:add,vacations');
        Route::post('/import-csv', [VacationController::class, 'importCsv'])->middleware('permission:add,vacations');
        Route::post('/update/{id}', [VacationController::class, 'update'])->middleware('permission:edit,vacations');
        Route::get('/list/{id}', [VacationController::class, 'getVacationsByEmployee'])->middleware('permission:list,vacations');
        Route::get('/list', [VacationController::class, 'getVacations'])->middleware('permission:list,vacations');
        Route::delete('/delete/{id}', [VacationController::class, 'destroy'])->middleware('permission:delete,vacations');
        Route::get('/{id}', [VacationController::class, 'getVacation'])->middleware('permission:view,vacations');
    });

    Route::group(['prefix' => '/dashboard'], function () {
        Route::get('/vacations', [DashboardController::class, 'getEmployeeInVacation'])->middleware('permission:admin,dashboard');
        Route::get('/incoming-vacations', [DashboardController::class, 'getIncomingVacations'])->middleware('permission:admin,dashboard')->name('incoming-vacations');
        Route::get('/maintenances/incoming', [DashboardController::class, 'getIncoming'])->middleware('permission:admin,dashboard');
        Route::get('/maintenances/recent', [DashboardController::class, 'getRecent'])->middleware('permission:admin,dashboard');
        Route::get('/maintenances', [DashboardController::class, 'getMaintenance'])->middleware('permission:admin,dashboard');
        Route::get('/admin', [DashboardController::class, 'getAdminDashboard'])->middleware('permission:admin,dashboard');
    });

    Route::group(['prefix' => '/roles'], function () {
        Route::get('/list', [RoleController::class, 'list'])->middleware('permission:list,roles');
        Route::post('/store', [RoleController::class, 'store'])->middleware('permission:add,roles');
        Route::post('/update/{id}', [RoleController::class, 'update'])->middleware('permission:edit,roles');
        Route::delete('/delete/{id}', [RoleController::class, 'delete'])->middleware('permission:delete,roles');
    });

    Route::group(['prefix' => '/permissions'], function () {
        Route::get('/list', [PermissionController::class, 'list'])->middleware('permission:list,permissions');
        Route::post('/store', [PermissionController::class, 'store'])->middleware('permission:add,permissions');
        Route::post('/update/{id}', [PermissionController::class, 'update'])->middleware('permission:edit,permissions');
        Route::delete('/delete/{id}', [PermissionController::class, 'delete'])->middleware('permission:delete,permissions');
    });

    Route::group(['prefix' => '/assets'], function () {
        Route::get('/list', [AssetController::class, 'list'])->middleware('permission:list,assets');
        Route::get('/{id}/components', [AssetController::class, 'getComponents'])->middleware('permission:list,assets');
        Route::post('/store', [AssetController::class, 'store'])->middleware('permission:add,assets');
        Route::post('/update/{id}', [AssetController::class, 'update'])->middleware('permission:edit,assets');
        Route::delete('/delete/{id}', [AssetController::class, 'delete'])->middleware('permission:delete,assets');
    });

    Route::group(['prefix' => '/components'], function () {
        Route::get('/list', [ComponentController::class, 'list'])->middleware('permission:list,components');
        Route::post('/store', [ComponentController::class, 'store'])->middleware('permission:add,components');
        Route::post('/update/{id}', [ComponentController::class, 'update'])->middleware('permission:edit,components');
        Route::delete('/delete/{id}', [ComponentController::class, 'delete'])->middleware('permission:delete,components');
    });

    Route::group(['prefix' => '/reports'], function () {
        Route::get('/statistics', [\App\Http\Controllers\ReportController::class, 'statistics'])->middleware('permission:list,reports');
        Route::get('/monthly-payable', [\App\Http\Controllers\ReportController::class, 'monthlyPayable'])->middleware('permission:list,reports');
    });
});
Route::group(['prefix' => '/maintenances'], function () {
    Route::get('/list', [MaintenanceController::class, 'list'])->middleware('permission:list,maintenance');
    Route::post('/store', [MaintenanceController::class, 'store'])->middleware('permission:add,maintenance');
    Route::post('/update/{id}', [MaintenanceController::class, 'update'])->middleware('permission:edit,maintenance');
    Route::delete('/delete/{id}', [MaintenanceController::class, 'delete'])->middleware('permission:delete,maintenance');

    Route::get('/recent', [MaintenanceController::class, 'recent'])->middleware('permission:list,maintenance');

})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'zk-mobile'], function () {
    Route::post('/login', [\App\Http\Controllers\ZKMobileApiController::class, 'login']);


    Route::get('/employees', [\App\Http\Controllers\ZKMobileApiController::class, 'getEmployees']);
    Route::get('/attendance', [\App\Http\Controllers\ZKMobileApiController::class, 'getAttendance']);

});

Route::group(['prefix' => 'zk-assignments'], function () {
    Route::get('/zk-employees', [ZKAssignmentController::class, 'getZKEmployees']);
    Route::get('/unlinked-employees', [ZKAssignmentController::class, 'getUnlinkedEmployees']);
    Route::get('/available-zk', [ZKAssignmentController::class, 'getAvailableZKEmployees']);
    Route::post('/link', [ZKAssignmentController::class, 'linkEmployee']);
    Route::post('/release', [ZKAssignmentController::class, 'releaseAssignment']);
    Route::get('/history/{employee_id}', [ZKAssignmentController::class, 'getEmployeeHistory']);
    Route::get('/attendance-history/{employee_id}', [ZKAssignmentController::class, 'getAttendanceHistory']);
    Route::get('/attendance-by-date/{date}', [ZKAssignmentController::class, 'getAttendanceByDate']);
    Route::get('/zk-history/{zk_employee_id}', [ZKAssignmentController::class, 'getZKHistory']);
});

/** COMPANIES */
Route::group(['prefix' => 'companies'], function () {
    Route::get('/list', [CompanyController::class, 'index'])->middleware('permission:list,companies');
    Route::post('/store', [CompanyController::class, 'store'])->middleware('permission:add,companies');
    Route::get('/show/{company}', [CompanyController::class, 'show'])->middleware('permission:list,companies');
    Route::post('/update/{company}', [CompanyController::class, 'update'])->middleware('permission:edit,companies');
    Route::delete('/delete/{company}', [CompanyController::class, 'destroy'])->middleware('permission:delete,companies');
})->middleware('auth:sanctum');

/** REAL LOGISTICS INVOICES */
Route::group(['prefix' => 'real-logistics-invoices'], function () {
    Route::get('/list', [RealLogisticsInvoiceController::class, 'index'])->middleware('permission:list,logistics');
    Route::post('/store', [RealLogisticsInvoiceController::class, 'store'])->middleware('permission:add,logistics');
    Route::get('/show/{realLogisticsInvoice}', [RealLogisticsInvoiceController::class, 'show'])->middleware('permission:list,logistics');
    Route::post('/update/{realLogisticsInvoice}', [RealLogisticsInvoiceController::class, 'update'])->middleware('permission:edit,logistics');
    Route::delete('/delete/{realLogisticsInvoice}', [RealLogisticsInvoiceController::class, 'destroy'])->middleware('permission:delete,logistics');
    Route::post('/preview-pdf', [RealLogisticsInvoiceController::class, 'previewPdf'])->middleware('permission:preview,logistics');
    Route::get('/export-pdf/{id}', [RealLogisticsInvoiceController::class, 'exportPdf'])->middleware('permission:download,logistics');
})->middleware('auth:sanctum');

/** CASHBOOK MODULE */
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Cashbooks
    Route::group(['prefix' => 'cashbooks'], function () {
        Route::get('', [CashbookController::class, 'index'])->middleware('permission:list,cashbooks');
        Route::post('/', [CashbookController::class, 'store'])->middleware('permission:add,cashbooks');
        Route::get('/{id}', [CashbookController::class, 'show'])->middleware('permission:list,cashbooks');
        Route::delete('/{id}', [CashbookController::class, 'destroy'])->middleware('permission:delete,cashbooks');
        Route::get('/{id}/summary', [CashbookController::class, 'summary'])->middleware('permission:list,cashbooks');

        // Transactions within a cashbook
        Route::get('/{id}/transactions', [TransactionController::class, 'index'])->middleware('permission:list,transactions');
        Route::post('/{id}/transactions', [TransactionController::class, 'store'])->middleware('permission:add,transactions');
    });

    // Standalone Transactions
    Route::group(['prefix' => 'transactions'], function () {
        Route::put('/{id}', [TransactionController::class, 'update'])->middleware('permission:edit,transactions');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->middleware('permission:delete,transactions');
    });
});

