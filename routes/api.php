<?php

use App\Http\Controllers\Api\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoordinateController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\StudentController;

// get the latest coordinate from the coordinates database
Route::get('/coordinate', [CoordinateController::class,'index']);
// post the collection of coordinate from driver divice or iot device to coordinates database
// is_expect_json is alias of middleware, that perform checking the request accept-header to be application/json
Route::middleware(['is_expect_json'])->post('/coordinate', [CoordinateController::class,'store']);

// login api end-point
Route::post('/login', [AuthController::class, 'login']);

// this route is for testing
// purpose is to dynamically create a bus{x}_locations table
Route::get('/create/bus', [BusController::class, 'createBus']);


// this buses route is use for 2 purpose:
// - retrieve the latest location from specific bus
// - retrieve the the current bus status
// - store the lastest location to the specific bus
Route::post('/bus/location', [BusController::class, 'getLatestLocation']);
Route::post('/bus/status', [BusController::class, 'getBusStatus']);
Route::post('/bus/status/update', [BusController::class, 'updateBusStatus']);
Route::post('/bus/location/create', [BusController::class, 'storeBusLocation']);
// get specific student and there current attendance-status info
Route::post('/student', [StudentController::class, 'getStudentInfo']);
// this attendance routes is use for 2 purpose:
// - retrieve for current student attendance status
// - store the latest student attendance
Route::post('/attendance', [AttendanceController::class, 'get']);
Route::post('/attendance/create', [AttendanceController::class, 'store']);
