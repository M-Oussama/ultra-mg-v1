<?php

use App\Http\Controllers\CertifyInvoiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
Route::post('/certifyInvoices/store', [CertifyInvoiceController::class, 'store'])->name('store');
Route::get('/certifyInvoices/getData', [CertifyInvoiceController::class, 'getData'])->name('getClientProductData');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
