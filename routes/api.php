<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoordinateController;

Route::get('/coordinate', [CoordinateController::class,'index']);

Route::post('/coordinate', [CoordinateController::class,'store']);
