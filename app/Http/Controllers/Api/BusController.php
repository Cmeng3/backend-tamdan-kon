<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusController extends Controller
{   
    // get all bus
    public function getBuses() {
        $buses = Bus::get();
        return response()->json([
            'buses' => $buses,
        ]);
    }
    //
    public function getBusesAndDriver() {
        $buses = Bus::get();
        // store the list of driver_id associated with bus
        // it could be driver with id = 0, mean no driver
        $drivers_id = [];
        foreach($buses as $bus) {
            $drivers_id[] = $bus->driver_id;
        }
        // store the list of drivers that have a bus
        $drivers = User::whereIn('id', $drivers_id)->get();
        $k = 0;
        foreach($drivers_id as $index => $id) {
            // index of drivers, to avoid repeart iteration through whole array again
            
            if($id == 0) {
                $buses[$index]['driver_name'] = 'no driver';
            } else {
                $buses[$index]['driver_name'] = $drivers[$k]->name;
                $k++;
            }
        }
        return response()->json([
            'driver_id' => $drivers_id,
            'drivers' => $drivers,
            'data' => $buses,
        ]);
    }
    // get specific bus location from corresponse database
    public function getLatestLocation(Request $request) {
        $bus_id = $request->input('busId');
        $table = 'bus'.$bus_id.'_locations';
        $location = DB::table($table)->get()->last();
        // if there no location data yet, return the below response
        if($location == null) {
            $lat = '11.587098';
            $lng = '104.844813';
        } else { 
            $lat = $location->latitude;
            $lng = $location->longitude;
        }
        // but if it have a data, return the below response
        return response()->json(
            [
                'lat'=> $lat,
                'lng'=> $lng,
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
    // get the bus Id
    public function getBusId(Request $request) {
        // extract the driverId from reqeust-body
        $driver_id = $request->input('driverId');
        // get the bus that associate with driver id
        $bus = Bus::where('driver_id', $driver_id)->first();
        // handle if there no bus
        if(!$bus) {
            return response()->json([
                'message' => 'there no bus associate with the driver yet',
                'bus_id' => 0,
                'driver_id' => $driver_id,
            ]);
        }
        $bus_id = $bus->id;
        return response()->json([
            'bus_id' => $bus_id,
        ]);
    }
    //
    public function getBusPolyline(Request $request) {
        $bus_id = $request->input('busId');
        $bus = Bus::where('id', $bus_id)->first();
        // check if there is the bus
        if(!$bus) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'there no bus associate with the driver', 
            ], 400);
        }
        return response()->json([
            'direction' => $bus->direction,
            'polyline' => $bus->polyline
        ]);

    }
    // store specific bus location to it corresponse database
    // in future developement we will add 2 things:
    // - validate the the request's data
    // - checking for authentication: token of bus driver or iot-box api key
    public function storeBusLocation(Request $request){
        // in future, there will be a validation of each field before inserting into database
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $bus_id = $request->input('busId');
        $table = 'bus'.$bus_id.'_locations';
        // handle the query with try-catch block, becuase there could be possible that laravel throw an QueryException
        try{
            $data = DB::table($table)->insert([
            'latitude' => $lat,
            'longitude' => $lng,
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        } catch(QueryException $e) {
            return response()->json([
                'message' => 'error occure during query',
                'error' => $e,
            ], 500);
        }
        return response()->json([
                'data'=> $data,
            ]);     
    }
    // 
    public function assignDriverABus(Request $request){
        $bus_id = $request->busId;
        $driver_id = $request->driverId;

        // remove the driver from previous bus if have
        $bus = Bus::where('driver_id', $driver_id)->first();
        if($bus) {
            $bus->driver_id = 0;
            $bus->save();
        }

        // assign driver to that specific bus
        $bus = Bus::where('id', $bus_id)->first();
        $bus->driver_id = $driver_id;
        $bus->save();

        return response()->json([
            'message' => 'bus assign successful',
        ]);
       
    }
    // update the bus polyline, during get route direction
    public function updateBusPolyline(Request $request) {
        $bus_id = $request->input('busId');
        $direction = $request->input('direction');
        $polyline = $request->input('polyline');
        // check if there a bus exist
        $bus = Bus::where('id', $bus_id)->first();
        if(!$bus) {
            return response()->json([
            'message' => 'bad request',
            'error' => 'there no bus associate with the driver',  
            ], 400);
        }
        // later in future we will validate this field first, before inserting into database
        $bus->direction = $direction;
        $bus->polyline = $polyline;
        // update the bus-polyline
        $bus->save();
        
        return response()->json([
            'message' => 'bus polyline update successfully',
        ]);
    }
    // update the specific bus status
    public function updateBusStatus(Request $request) {
        // get the specific driver id from request body
        $driver_id = $request->input('driverId');
        $bus = Bus::where('driver_id', $driver_id)->first();
        // if the $bus is null, it mean driver have no assign bus yet
        // becuase there no bus-recored associated with this driver-id
        if(!$bus){
            return response()->json(
                [
                'message' => 'bad request',
                'error' => 'driver have no assign bus yet',
                ], 
            400);
        }
        // show the current bus operation
        $bus_status = 'bus is currently operate';
        // switching on-off on bus-active-status
        if($bus->is_active == 0){
            $bus->is_active = true;
        } else {
            $bus_status = 'bus is currently stop operate';
            $bus->is_active = false;
        }
        // update the bus-active-status
        $bus->save();
        // return the response
        return response()->json([
            'message' => $bus_status,
        ]);
        
    }
    // for testing dynamically creating bus_locations table
    // this method will later use to create a new bus locations database
    public function createBus(Request $request) {
        $bus_name = $request->input('busName');
        $driver_id = 0;
        
        if($bus_name == null) {
            $bus_name = 'no_name';
        }
        // creating the new bus with driver_id and bus_name
        $bus = Bus::create([
            'bus_name' => $bus_name,
            'driver_id' => $driver_id
        ]);
        // create new bus location table
        $table = 'bus'.$bus->id.'_locations';
        $stm = "
            CREATE TABLE `$table` (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                latitude VARCHAR(20),
                longitude VARCHAR(20),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $create_bus_locations_table = DB::statement($stm);
        // create new chat table
        $table = 'chat'.$bus->id;
        $stm = "
            CREATE TABLE `$table` (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                message VARCHAR(1024),
                user_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        $create_chat_table = DB::statement($stm);

        return response()->json(
            [
                'message' => 'create table success',
                'bus_location_table' => $create_bus_locations_table,
                'chat_table' => $create_chat_table,
            ]
        );
    }
}
