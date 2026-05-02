<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coordinate;

class CoordinateController extends Controller
{
    public function index() {
       // ::all() will retrieve all record from db and return as Eloquent collection
       $latest_coordinate = Coordinate::all()->last();
        
        return response()->json([
            'message' => 'successful get coordinate',
            'data' => $latest_coordinate->latitude.','.$latest_coordinate->longitude,
        ]); 
    }

    public function store(Request $request) {

        // validate the request data
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
       
       // store the coordinate in the from the $request object
       $latitude =$request->input('latitude');
       $longitude =$request->input('longitude');

       
       // stoe the coordinate in the database
       Coordinate::create([
        'latitude' => $latitude,
        'longitude' => $longitude,
       ]);

        // sending the response message
       return response()->json([
        'message' => 'successful store coordinate',
        'data' => $latitude.','.$longitude,
       ]);
    }
}