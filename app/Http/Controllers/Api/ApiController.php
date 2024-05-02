<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class ApiController extends Controller
{
    // POST [name, email, password]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation Errors',
                    'errors' => [$validator->errors()],
                ], 400
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Created Successfully',
            'data' => $user,
        ]);
    }
// POST [ email, password ]
    public function login(Request $request)
    {

        $request->validate([
            "email" => "required|email|string",
            "password" => "required",
        ]);

        // User object
        $user = User::where("email", $request->email)->first();

        if (!empty($user)) {

            // User exists
            if (Hash::check($request->password, $user->password)) {

                // Password matched
                $token = $user->createToken("mytoken")->accessToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    "token" => $token,
                    "data" => [],
                ]);
            } else {

                return response()->json([
                    "status" => false,
                    "message" => "Password didn't match",
                    "data" => [],
                ]);
            }
        } else {

            return response()->json([
                "status" => false,
                "message" => "Invalid Email value",
                "data" => [],
            ]);
        }
    }

    // GET [Auth: token]
    public function profile()
    {
        $user = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "User Profile Information",
            "data" => $user,
        ]);
    }

    // GET [Auth: token]
    public function logout()
    {
        $token = auth()->user()->token();
        $token->revoke();
        return response()->json([
            "status" => true,
            "message" => "User Logout Successfully",
            "data" => [],
        ]);
    }
}
