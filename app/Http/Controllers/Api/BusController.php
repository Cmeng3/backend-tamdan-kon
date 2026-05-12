<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusController extends Controller
{
    // get specific bus location from corresponse database
    public function getLatestLocation(Request $request) {
        $bus_id = $request->input('busId');
        $table = 'bus'.$bus_id.'_locations';
        $location = DB::table($table)->get()->last();
        // if there no location data yet, return the below response
        if($location == null) {
            return response()->json(
                [
                    'message' => 'no_data',
                ],
                400
            );
        }
        // but if it have a data, return the below response
        return response()->json(
            [
                'lat'=> $location->latitude,
                'lng'=> $location->longitude,
            ]
        );
    }
    // get the current specific bus-status
    public function getBusStatus(Request $request) {
        // get the bus specific id
        $bus_id = $request->input('busId');
        // if the bus_id is 0, it mean student don't have a bus yet
        if($bus_id == 0) {
            $bus_status = 0;
        }
        // query the bus status
        $bus = Bus::where('id', $bus_id)->first();
        $bus_status = $bus->is_active;

        return response()->json([
            'bus_status' => $bus_status,
        ]);
    }
    // store specific bus location to it corresponse database
    // in future developement we will add 2 things:
    // - validate the the request's data
    // - checking for authentication: token of bus driver or iot-box api key
    public function storeBusLocation(Request $request){
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $bus_number = $request->input('busId');
        $table = 'bus'.$bus_number.'_locations';
        $data = DB::table($table)->insert([
            'latitude' => $lat,
            'longitude' => $lng,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(
            [
                'data'=> $data,
            ]
        );
        
    }
    // for testing dynamically creating bus_locations table
    // this method will later use to create a new bus locations database
    public function createBus() {
        $create_table = DB::statement('CREATE TABLE bus2_location (ID INT PRIMARY KEY, Name VARCHAR(50));');

        return response()->json(
            [
                'message' => 'create table success',
                'data' => $create_table,
            ]
        );

    }
}
