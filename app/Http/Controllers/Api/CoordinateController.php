<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coordinate;

class CoordinateController extends Controller
{
    public function index() {
        
       $latest_coordinate = Coordinate::orderBy('id', 'desc')->first();
        
        return response()->json([
            'message' => 'successful get coordinate',
            'data' => $latest_coordinate->latitude.','.$latest_coordinate->longitude,
        ]); 
    }

    public function store(Request $request) {

        // check if the request header accept a json response
        // to ensure the validation return the json message instead of redirect to the previous page
        if(in_array('application/json', $request->getAcceptableContentTypes())) {
            return response()->json([
                'message' => 'Content-Type must be application/json',
            ], 400);
        }

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