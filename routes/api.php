<?php

use App\Http\Controllers\CertifyInvoiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
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
Route::get('/pos/sales/getData', [POSController::class, 'getData'])->name('getData');
Route::post('/pos/sales/store', [POSController::class, 'store'])->name('store');
Route::get('/pos/sale/getSale/{id}', [POSController::class, 'getSale'])->name('getSale');
Route::get('/pos/sale/getSaleData/{id}', [POSController::class, 'getSaleData'])->name('getSale');
Route::post('/pos/sales/update/{id}', [POSController::class, 'update'])->name('update');
Route::post('/pos/sales/payment/create/{id}', [POSController::class, 'addPayment'])->name('addPayment');
Route::get('/pos/sales/payments/list', [POSController::class, 'listPayment'])->name('listPayment');
Route::post('/pos/sales/payment/create', [POSController::class, 'createPayment'])->name('createPayment');
Route::post('/pos/sales/payment/update', [POSController::class, 'updatePayment'])->name('updatePayment');
Route::post('/pos/sales/payment/delete', [POSController::class, 'deletePayment'])->name('deletePayment');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
