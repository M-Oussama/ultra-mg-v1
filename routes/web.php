<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/** USERS  */
Route::get('/apps/users/list', [UserController::class, 'getUsers'])->name('getUsers');
Route::post('/apps/users/store', [UserController::class, 'store'])->name('storeUser');


Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');



