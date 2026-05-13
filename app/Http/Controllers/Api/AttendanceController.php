<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // get the current student-attendance status
    public function get(Request $request) {
        // store the specific student-id from request body
        $student_id = $request->input('studentId');
        // fetch the current student attendance-status
        $attendance = Attendance::whereDate('created_at', today())->where('student_id', $student_id)->first();
        // checking for the student-attendance data  result
        if($attendance == null) {
            $attendance_status = 'absent';
        } else {
            $attendance_status = 'present';
        }
        // response back the data
        return response()->json([
            'attendance_status' => $attendance_status,
        ]);
    }
    // create the new new student-attendance for a new-day
    public function store(Request $request) {
        // get the specific student-id from the request body
        $student_id = $request->input('studentId');
        // fetch the specific attendance
        $attendance = Attendance::whereDate('created_at', today())->where('student_id', $student_id)->first();
        // checking for student-attendance create status
        if($attendance == null){
            Attendance::create([
                'student_id' => $student_id,
            ]);
            $is_attendance_update = 'attendance scanned';
        } else{
            $is_attendance_update = 'attendance already scan';
        }
        // return the response
        return response()->json(
            [
                'message' => $is_attendance_update,
            ]
        );
    }
}
