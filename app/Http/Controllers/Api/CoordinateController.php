<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoordinateController extends Controller
{
    public function store(Request $request) {
        return response()->json([
            'message' => 'successful store coordinate',
            'data' =>  '11.587092,104.844918',
        ]); 
    }
}
