<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // get all messages from specific-chat-table
    public function get(Request $request){
        // extract the userId from request
        // and retrieve the User() instance, to check for the role
        $user_id = $request->input('userId');
        $user = User::where('id', $user_id)->first();
        // check if the user is exist or not
        if(!$user) {
            return response()->json([
                'message' => 'bad request',
                'error' => 'there are no user with this id'
            ], 400);
        }
        // chat-id is equivalent the bus-id
        $chat_id = 0;
        // alternative way to get the chat-id
        if($user->role == 'parent') {
            $student = Student::where('parent_id', $user_id)->first();
            // chat could be null, do to child doesn't have a bus yet
            $chat_id = $student->bus_id;
        } elseif($user->role == 'driver') {
            // $chat_id could be null, do to bus_driver does have a bus yet
            $chat_id = Bus::where('driver_id', $user_id)->first();
        } else {
            // if user is the admin, we extract chat-id directly from request
            $chat_id = $request->input('chatId');
        }
        // check if the chat-id exist or not
        // if not return the empty message
        if($chat_id == 0) {
            return response()->json([
                'message' => 'there are no available chat-box',
                'data' => 'not_available'
            ]);
        }
        // when reaching through here, the chat_id is safe to query
        // because we retrieve chat_id through laravel-model query, which auto prevent sql-injection
        $table = "chat$chat_id";
        $chat_messages = DB::table($table)->get();
        $format_messages = [];
        // parse the chat_messages to another array format
        foreach($chat_messages as $msg) {
            // there must be a user associate with this message
            // because we treat the chat-row associate with user_id as foriegn-key
            $user = User::where('id', $msg->user_id)->first();
            $username = $user->name;
            $message = [
                'name' => $username,
                'message' => $msg->message,
                'isMe' => ($msg->user_id == $user_id),
                'time' => $msg->created_at,
            ];    
            $format_messages[] = $message;
        }
        // clear out the resource
        $chat_messages = [];
        // return the response
        return response()->json([
            'message' => 'chat retrieve successful',
            'data' => $format_messages,
        ]);
    }
    // store the chat message to specific table
    public function store(Request $request) {
        // extract the userId
        // get the user
        $user_id = $request->input('userId');
        $user = User::where('id', $user_id)->first();
        // 
        $chat_id = 0;
        // alternative way to get the chat-id
        if($user->role == 'parent') {
            $student = Student::where('parent_id', $user_id)->first();
            // chat could be null, do to child doesn't have a bus yet
            $chat_id = $student->bus_id;
        } elseif($user->role == 'driver') {
            // $chat_id could be null, do to bus_driver does have a bus yet
            $chat_id = Bus::where('driver_id', $user_id)->first();
        } else {
            // if user is the admin, we extract chat-id directly from request
            $chat_id = $request->input('chatId');
        }
        // we don't treat is as error, because parent and driver are possibly to not have a chat-box
        // due to not assigned to any specific bus
        if($chat_id == 0) {
            return response()->json([
                'message' => 'user doesn\'t have a chat-box yet',
            ]);
        }
        $table = "chat$chat_id";
        // extract a message from the request-body
        $message = $request->input('message');
        // this laravel query builder, are naturally prevent sql-injection
        // should wrape this in try-catch block, because it can  be a column-violation sql-exception
        $success = DB::table($table)->insert([
            'user_id' => $user_id,
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // conditionally return the response
        if($success) {
            return response()->json([
                'message' => 'user successfully send the message'
            ]);
        } else {
            return response()->json([
                'message' => 'user unsuccess to send a message'
            ], 500);
        }
    }
}
