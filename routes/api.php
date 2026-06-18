<?php

use App\Http\Controllers\Api\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoordinateController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\UserController;

// get the latest coordinate from the coordinates database
Route::get('/coordinate', [CoordinateController::class,'index']);
// post the collection of coordinate from driver divice or iot device to coordinates database
// is_expect_json is alias of middleware, that perform checking the request accept-header to be application/json
Route::middleware(['is_expect_json'])->post('/coordinate', [CoordinateController::class,'store']);

// this route is for testing
// purpose is to dynamically create a bus{x}_locations table
Route::get('/create/bus', [BusController::class, 'createBus']);


// login api end-point
Route::post('/login', [AuthController::class, 'login']);


// this is user-controller
Route::post('/parent', [UserController::class, 'getParentsInfo']);
Route::post('/driver', [UserController::class, 'getDriversInfo']);
Route::post('/parent/create', [UserController::class, 'createParentAccount']);
Route::post('/driver/create', [UserController::class, 'createDriverAccount']);
Route::post('/user/update', [UserController::class, 'update']);
// this buses route is use for 3 purpose:
// - retrieve the latest location from specific bus
// - retrieve the the current bus status
// - store the lastest location to the specific bus
Route::post('/bus/all', [BusController::class, 'getBuses']);
Route::post('/bus/driver/all', [BusController::class, 'getBusesAndDriver']);
Route::post('/bus/location', [BusController::class, 'getLatestLocation']);
Route::post('/bus/status', [BusController::class, 'getBusStatus']);
Route::post('/bus/id', [BusController::class, 'getBusId']);
Route::post('/bus/polyline', [BusController::class, 'getBusPolyline']);
Route::post('/bus/assign', [BusController::class, 'assignDriverABus']);
Route::post('/bus/polyline/update', [BusController::class, 'updateBusPolyline']);
Route::post('/bus/status/update', [BusController::class, 'updateBusStatus']);
Route::post('/bus/location/create', [BusController::class, 'storeBusLocation']);
Route::post('/bus/create', [BusController::class, 'createBus']);
// get specific student and there current attendance-status info
Route::post('/student', [StudentController::class, 'getStudentInfo']);
Route::post('/student/id', [StudentController::class, 'getStudentId']);
Route::post('/student/info/bus', [StudentController::class, 'getAllStudentOnBus']);
Route::post('/student/bus', [StudentController::class, 'getStudentsInfoOnBusToday']);
Route::post('/student/update/bus', [StudentController::class, 'updateStudentABus']);
Route::post('/student/update/location', [StudentController::class, 'updateStudentLocation']);
// this attendance routes is use for 6 purpose:
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
Route::post('/attendance/request/all', [AttendanceController::class, 'getAllStudentsAttendanceRequest']);
Route::post('/attendance/request/create', [AttendanceController::class, 'storeAttendanceRequest']);
Route::post('/attendance/request/update', [AttendanceController::class, 'updateAttendanceRequestStatus']);
// this chat routes is use for X purpose:
// - retrieve all-chat-messages from specific chat-table
// - store the user message to specific chat-table
Route::post('/chat', [ChatController::class, 'get']);
Route::post('/chat/store', [ChatController::class, 'store']);
