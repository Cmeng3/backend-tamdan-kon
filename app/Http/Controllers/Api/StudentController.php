<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Bus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // get the specific student from their parent_id
    public function getStudentInfo(Request $request)
    {
        // get the parent-id and query it children
        $parent_id = $request->parentId;
        $student = Student::where('parent_id', $parent_id)->first();
        // check their existing of student with their parent-id
        if (!$student) {
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
        if ($attendance) {
            $attendance_status = 'present';
        } else {
            $attendance_status = 'absent';
        }
        // check if the student have a bus yet
        if ($student->bus_id == 0) {
            $driver_name = 'student have no assign bus yet';
        }
        // get the student bus-driver name
        $student_bus = Bus::where('id', $student->bus_id)->first();
        // check if the bus have a driver yet
        if ($student_bus->driver_id == 0) {
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
    // get specific studentId base on parentId
    public function getStudentId(Request $request)
    {
        $parent_id = $request->input('parentId');
        $student = Student::where('parent_id', $parent_id)->first();

        if (!$student) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'there no student existing with this parent'
            ], 400);
        }

        return response()->json([
            'student_id' => $student->id,
        ]);
    }
    // get all student on that specific bus
    public function getAllStudentOnBus(Request $request) {
        $driver_id = $request->input('driverId');
        $bus = Bus::where('driver_id', $driver_id)->first();
        // check if there no bus associated with driver
        if(!$bus) {
            return response()->json([
              'message' => 'bad request',
              'error' => 'there no bus associate with driver'  
            ], 400);
        }
        $bus_id = $bus->id;
        $students = Student::where('bus_id', $bus_id)->get();
        // store the list of parent-id that associate with student
        $parents_id_list = [];
        foreach($students as $student) {
            $parents_id_list[] = $student->parent_id;
        } 
        // get all parent associate with students
        $parents = User::whereIn('id', $parents_id_list)->get();
         // create the new student_info list
        foreach($parents as $index => $parent) {
            $students[$index]['parent_name'] = $parent['name'];
            $students[$index]['phone_number'] = $parent['phone_number'];
        } 
        // clear out the data
        $parents = [];
        $parents_id_list = [];
        return response()->json([
            'students' => $students,
        ]);
    }
    // get the current students information on specific bus
    // it will show all student with it respective status on the bus today
    // this function cuase: 3-sql-query and O(3N) iteration of array with N number of student
    public function getStudentsInfoOnBusToday(Request $request)
    {
        $driver_id = $request->input('driverId');
        // get the bus info that driver is own
        $bus = Bus::where('driver_id', $driver_id)->first();
        // if there no bus associate with this driver
        if (!$bus) {
            return response()->json([
                'message' => 'driver have no bus yet',
                'student' => []
            ]);
        }
        $bus_id = $bus->id;
        // this could possible to return the empty list of students
        $students = Student::where('bus_id', $bus_id)->get();

        // get list of student_id
        $studentId_list = [];
        foreach ($students as $student) {
            $studentId_list[] = $student->id;
        }

        // get all students attendance, who are 'present' and ask for permission
        $attendances = Attendance::whereDate('created_at', today())->whereIn('student_id', $studentId_list)->get();
        $attendance_request = AttendanceRequest::whereDate('created_at', today())->whereIn('student_id', $studentId_list)->where('status', 0)->get();

        // creating the new list of student_attendance by mapping with 3 arrays above
        $students_attendance = [];
        foreach($studentId_list as $student) {
            // we must check the length of $attendances and $attendance_request, to prevent accessing non-exist element
            $pre_len = count($attendances);
            $pen_len = count($attendance_request);
            $present = 0;
            $pending = 0;
            if(($pre_len > 0) && ($present < $pre_len) && ($student == $attendances[$present]->student_id)) {
                $students_attendance[] = 'present';
                $present++;
            } elseif(($pen_len > 0) && ($pending < $pen_len) && ($student == $attendance_request[$pending]->student_id)) {
                $students_attendance[] = 'pending';
                $pending++;
            } else{
                $students_attendance[] = 'absent';
            }
        }

        // adding the present_status to existing, students[] array
        foreach($students as $index => $student) {
            $student['status'] = $students_attendance[$index];
            $students[$index] = $student;
        }
        return response()->json([
            'data' => $students,
        ]);
    }
    //
    public function updateStudentLocation(Request $request) {
        $student_id = $request->input('studentId');
        $latlng = $request->input('latlng');
        // we assumpt the student is exist
        // we assumpt the data is valid
        $student = Student::where('id', $student_id)->first();
        $student->latlng = $latlng;
        $data = $student->save();

        return response()->json([
            'message' => $data,
        ]);
    }
}
