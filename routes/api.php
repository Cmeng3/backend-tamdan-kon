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
Route::post('/student/id', [StudentController::class, 'getStudentId']);
// this attendance routes is use for 2 purpose:
// - retrieve for current student attendance status
// - retrieve all attendance records for a specific student
// - store the latest student attendance
// - retrieve the attendance request status 
// - store the new attendance request status
// - update the attendance request
Route::post('/attendance', [AttendanceController::class, 'get']);
Route::post('/attendances', [AttendanceController::class, 'getAll']);
Route::post('/attendance/create', [AttendanceController::class, 'store']);
Route::post('/attendance/request/status', [AttendanceController::class, 'getAttendanceRequestStatus']);
Route::post('/attendance/request/create', [AttendanceController::class, 'storeAttendanceRequest']);
Route::post('/attendance/request/update', [AttendanceController::class, 'updateAttendanceRequestStatus']);
