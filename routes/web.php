<?php

use App\Http\Controllers\UserController;
use App\Mail\NewEmployeeMail;
use Illuminate\Support\Facades\Mail;
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


Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
//
//Route::get('/test', function() {
//    $name = "Mahgoun Oussama";
//   Mail::to('mahgounoussama23@gmail.com')->send(new NewEmployeeMail($name));
//});

