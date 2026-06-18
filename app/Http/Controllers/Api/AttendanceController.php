<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Student;
use App\Models\User;
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
        // letter in future, will compare the students attendance record with the school semester schedule
        // purpose: to check the student is present at school specific schedule or not
        foreach($attendances as $key => $att){
            $att['is_present' ] = true;
            $attendances[$key] = $att;
        }
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
    // get a student request status
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
    // get all students request status on today
    public function getAllStudentsAttendanceRequest() {
        $all_request =  AttendanceRequest::whereDate('created_at', today())->where('status', false)->orderBy('student_id')->get();
        // get all student id
        $students_id = [];
        foreach($all_request as $request) {
            $students_id[] = $request->student_id;
        }
        // store the students info
        $students = Student::whereIn('id', $students_id)->get();

        //parents id
        $parents_id = [];
        foreach($students as $student) {
            $parents_id[] = $student->parent_id;
        }
        $parents = User::whereIn('id', $parents_id)->get();

        // let assume that all parents info, is other co-response to their children, becuase parent who register first, their children must position frist in the table
        // this concatenate information will false, when parent have multiple children, becuase children order might not match the parent order anymore
        foreach($students as $index => $student) {
            $all_request[$index]['student'] = $student->name;
            $all_request[$index]['bus_id'] = $student->bus_id;
            $all_request[$index]['parent'] = $parents[$index]->name;
        }

        // sort the order back to normal, by who request first appear first
        $all_request = collect($all_request)->sortBy('created_at')->values()->all();
        
        return response()->json([
            'attendances_request' => $all_request,
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
