<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
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
    //
    public function getAll(Request $request) {
        // we will implement more tommorow
        $student_id = $request->input('studentId');
        $attendances = Attendance::where('student_id', $student_id)->get();
        // it could be null, and we will impliment the condition to check it later
        return response()->json([
            'data' => $attendances,
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
    //
    public function getAttendanceRequestStatus(Request $request) {
        // extract student id from request-body
        $student_id = $request->input('studentId');
        $is_request_exist_today = AttendanceRequest::whereDate('created_at', today())->where('student_id', $student_id)->first();
        $attendance = Attendance::whereDate('created_at', today())->where('student_id', $student_id)->first();

        // storing a attendance-request status
        if($attendance){
            $status = 'confirm';
        }
        // if there no record yet, so it mean parents not yet ask for permission
        else if(!$is_request_exist_today) {
            $status = 'no_request';
        }
        // if there exist, let check the records status
        else if($is_request_exist_today->status == 0){
            $status = 'pending';
        } else {
            $status = 'confirm';
        }
        return response()->json([
            'status' => $status,
        ]);
    }
    // store the attendance-request permission record from parent
    public function storeAttendanceRequest(Request $request) {
        // get and extract studentId and reason from request-body
        $student_id = $request->input('studentId');
        $request_record = AttendanceRequest::whereDate('created_at', today())->where('student_id', $student_id)->first();
        // checking there is the permission-record yet
        if($request_record){
            $message = 'permission already asked';
        } else {
            $reason = $request->input('reason');
            AttendanceRequest::Create([
                'student_id' => $student_id,
                'reason' => $reason,
                'status' => false,
            ]);
            $message = 'attendance permission asking successful ';
        }
        // response to client
        return response()->json([
            'message' => $message,
        ]);
    }
    // 
    public function updateAttendanceRequestStatus(Request $request) {
        // extract student id from a request
        $student_id = $request->input('studentId');
        $attendanceRequest = AttendanceRequest::whereDate('created_at', today())->where('student_id', $student_id)->first();
        // checking if there request-record exist yet for today
        if(!$attendanceRequest){
            return response()->json([
                'message' => 'bad request',
                'error' => 'updated on a no exist student-attendance request',
            ], 400);
        }
        // if there an exist record today, let mark the status as confirm
        $attendanceRequest->status = true;
        $attendanceRequest->save();

        $attendance = Attendance::whereDate('created_at', today())->where('student_id', $student_id)->first();
        if(!$attendance) {
            Attendance::create([
                'student_id' => $student_id,
            ]);
        }

        return response()->json([
            'message' => 'student attendance request is now approved',
        ]);
    }   
}
