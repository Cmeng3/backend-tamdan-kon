<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoordinateController;

Route::get('/user', [CoordinateController::class,'store']);
