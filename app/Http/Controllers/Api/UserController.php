<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{   
    // get all parents info, with it children info
    public function getParentsInfo() {
        $parents = User::where('role', 'parent')->get();

        $parents_id = [];
        foreach($parents as $parent) {
            $parents_id[] = $parent['id'];
        }

        $students = Student::whereIn('parent_id', $parents_id)->get();

        foreach($students as $index => $student) {
            $parents[$index]['student_name'] = $student['name'];
            $parents[$index]['location'] = $student['latlng'];
            $parents[$index]['student_gender'] = $student['sex'];
            $parents[$index]['bus_number'] = $student['bus_id'];
        }

        return response()->json([
            'parents' => $parents,
        ]);
    }
    // get all driver infomation
    public function getDriversInfo() {
        $drivers = User::where('role', 'driver')->get();
        $drivers_id = [];
        // store all driver_id
        foreach($drivers as $driver) {
            $drivers_id[] = $driver->id;
        }
        // get all associate bus with driver
        $buses = Bus::whereIn('driver_id', $drivers_id)->get();
        // create a new list that store the driver_with it associate bus
        $driver_bus = [];
        foreach($drivers_id as $id) {
            $is_assign = false;
            $bus_id = 0;
            foreach($buses as $bus) {
                if($bus->driver_id == $id){
                    $is_assign = true;
                    $bus_id = $bus->id;
                }
            }
            
            $driver_bus[] = [
                'hasBus' => $is_assign,
                'busId' => $bus_id,
            ];
        }
        // adjust bus assign info to existing $drivers 
        foreach($driver_bus as $index => $bus) {
            $drivers[$index]['hasBus'] = $bus['hasBus'];
            $drivers[$index]['busId'] = $bus['busId'];
        }

        return response()->json([
            'data' => $drivers,
        ]);
    }
    // create an parent account with it student info
    public function createParentAccount(Request $request) {
        $parent = $request->only(['parent_name', 'email', 'phone_number', 'password']);

        // check if the email is unique
        $email = User::where('email', $parent['email'])->first();
        $phone_number = User::where('phone_number', $parent['phone_number'])->first();
        // check if eamil and phone number is unique
        if($email) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'email must be unique',
            ], 400);
        }
        if($phone_number) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'phone number must be unique',
            ], 400);
        }
        // re-format parent before create the data
        $parent = [
            'name' => $parent['parent_name'],
            'email' => $parent['email'],
            'phone_number' => $parent['phone_number'],
            'password' => $parent['password'],
            'role' => 'parent',
        ];
        $parent = User::create($parent);

        $student = $request->only(['student_name', 'location', 'sex', 'bus_number']);
        $student = [
            'parent_id' => $parent->id,
            'name' => $student['student_name'],
            'latlng' => $student['location'],
            'sex' => $student['sex'],
            'bus_id' => $student['bus_number'],
        ];
        $student = Student::create($student);
        
        return response()->json([
            'parent' => $parent,
            'student' => $student,
        ]);
    }
    // create driver account
    public function createDriverAccount(Request $request) {
        $driver_info = $request->only(['name', 'email', 'phone_number', 'password']);
        $driver_info['role'] = 'driver';

        // check if the email is unique
        $email = User::where('email', $driver_info['email'])->first();
        $phone_number = User::where('phone_number', $driver_info['phone_number'])->first();
        // check if eamil and phone number is unique
        if($email) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'email must be unique',
            ], 400);
        }
        if($phone_number) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'phone number must be unique',
            ], 400);
        }

        $driver = User::create($driver_info);
        // assign the driver to the bus
        $bus_id = $request->input('busId');
        if($bus_id != 0) {
            $bus = Bus::where('id', $bus_id)->first();
            $bus->driver_id = $driver->id;
            $bus->save();
        }

        return response()->json([
            'data' => $driver,
        ]);

    }
    // update specific user-information
    public function update(Request $request) {
        $user_id = $request->input('userId');
        $update_data = $request->input('updateData');
        $field_to_update = $request->input('updateField');
        $user = User::where('id', $user_id)->first();
        // check if there any user
        if(!$user) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'there no user with this id'
            ], 400);
        }
        // check what field to update
        // later in future, we will develop the field-validation
        if($field_to_update == 'username') {
            $user->name = $update_data;
        } else if($field_to_update == 'email') {
            $user->email = $update_data;
        } else if($field_to_update == 'password') {
            $user->password = Hash::make($update_data);
        } else if($field_to_update == 'phoneNumber') {
            $user->phone_number = $update_data;
        } else {
            return response()->json([
                'message' => 'bad request',
                'error' => 'there no existing field'
            ], 400);
        }
        // update the field
        if($user->save()) {
            // return the 200 response
            return response()->json([
                'message' => 'user update successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'there are no update',
            ]);
        }
    }
}
