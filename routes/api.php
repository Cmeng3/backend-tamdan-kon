<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoordinateController;
use App\Http\Controllers\Api\AuthController;
// get the latest coordinate from the coordinates database
Route::get('/coordinate', [CoordinateController::class,'index']);
// post the collection of coordinate from driver divice or iot device to coordinates database
// is_expect_json is alias of middleware, that perform checking the request accept-header to be application/json
Route::middleware(['is_expect_json'])->post('/coordinate', [CoordinateController::class,'store']);
// login api end-point
Route::post('/login', [AuthController::class, 'login']);