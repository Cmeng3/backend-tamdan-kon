<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // user login handler 
    public function login(Request $request) {
        // validate the login input: uasername and password
        $request->validate([
            'username' => 'required|string|min:5|max:50',
            'password' => 'required|string|min:8',
        ]);
        // query the user in database, if not found return null
        $user = User::where('name', $request->username)->first();
        // handle the wrong username and password input
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(
                [
                    'message'=>'Invalid credential'
                ]
            , 401);
        }
        // if above condition passed, token will be created and store it in $user_token
        $user_token = $user->createToken('mobile-token')->plainTextToken;
        // response back with the user information and token 
        return response()->json(
            [
                'message'=>'login successful',
                'user' => $user,
                'token' => $user_token,
            ]
        );
    }

    // later through out development, we will implements this handler
    public function register($request) {

    }


}
