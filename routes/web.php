<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ZKPushController;
use App\Mail\NewEmployeeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jmrashed\Zkteco\Lib\ZKTeco;

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




// ZKTeco iClock push endpoint: supports GET or POST depending on firmware
Route::match(['GET','POST'], '/iclock/cdata', [ZKPushController::class, 'handleIclock']);
// Some firmwares use /iclock/attlog to send ATTLOG entries
Route::match(['GET','POST'], '/iclock/attlog', [ZKPushController::class, 'handleIclock']);
// Auxiliary iClock endpoints some devices call periodically
Route::match(['GET','POST'], '/iclock/getrequest', [ZKPushController::class, 'handleIclockGetRequest']);
Route::match(['GET','POST'], '/iclock/devicecmd', [ZKPushController::class, 'handleIclockDeviceCmd']);

// Alternative JSON push endpoint using controller (recommended when device supports custom URL with uid/timestamp)
Route::post('/zk/push', [ZKPushController::class, 'handle']);

Route::get('/test', function (Request $request) {
    Log::info('test------');
});

Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');

//
//Route::get('/test', function() {
//    $name = "Mahgoun Oussama";
//   Mail::to('mahgounoussama23@gmail.com')->send(new NewEmployeeMail($name));
//});

