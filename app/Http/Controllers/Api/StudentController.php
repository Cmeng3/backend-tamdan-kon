<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Bus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // get the specific student from their parent_id
    public function getStudentInfo(Request $request) {
        // get the parent-id and query it children
        $parent_id = $request->parentId;
        $student = Student::where('parent_id', $parent_id)->first();
        // check their existing of student with their parent-id
        if(!$student) {
            return response()->json(
                [   
                    'message' => 'bad request',
                    'error' => 'no student exist with this parent',
                ],
                400
            );
        }
        // check for student today attendance
        $attendance = Attendance::whereDate('created_at', today())->where('student_id', $student->id)->first();
        if($attendance){
            $attendance_status = 'present';
        } else{
            $attendance_status = 'absent';
        }
        // check if the student have a bus yet
        if($student->bus_id == 0){
            $driver_name = 'student have no assign bus yet';
        } 
        // get the student bus-driver name
        $student_bus = Bus::where('id', $student->bus_id)->first();
        // check if the bus have a driver yet
        if($student_bus->driver_id == 0){
            $driver_name = 'bus have no driver';
        }
        $driver = User::where('id', $student_bus->driver_id)->first();
        $driver_name = $driver->name;
        return response()->json([
            'data' => $student,
            'attendance_status' => $attendance_status,
            'driver_name' => $driver_name
        ]);
    }
    //
    public function getStudentId(Request $request) {
        $parent_id = $request->input('parentId');
        $student = Student::where('parent_id', $parent_id)->first();

        if(!$student){
            return response()->json([
                'message' => 'bad request',
                'error' => 'there no student existing with this parent'
            ], 400);
        }

        return response()->json([
            'student_id' => $student->id,
        ]);
    }
}
