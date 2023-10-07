<?php

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
