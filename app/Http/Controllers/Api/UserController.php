<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
