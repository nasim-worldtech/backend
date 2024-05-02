<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class ApiController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseJson(400, false, 'Validation Errors', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $this->responseJson(201, true, 'User Created Successfully', $userData);
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|string",
            "password" => "required|min:6",
        ]);

        $user = User::where("email", $request->email)->first();

        if (empty($user)) {
            // User not found
            return $this->responseJson(401, false, "Invalid Login Credentials");
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->responseJson(401, false, "Incorrect Password");
        }

        // Create token
        $token = $user->createToken('mytoken')->accessToken;

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return response()->json([
            'status_code' => 200,
            'success' => true,
            'message' => "Login successful",
            'token' => $token,
            'data' => $userData,
        ], 200);
    }

    public function profile()
    {
        $user = auth()->user();
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $this->responseJson(200, true, "User Profile Information", $userData);
    }

    public function logout()
    {
        $token = auth()->user()->token();
        if ($token) {
            $token->revoke();
            return $this->responseJson(200, true, "User Logout Successfully");
        } else {
            return $this->responseJson(401, false, "User is not logged in");
        }
    }
}
