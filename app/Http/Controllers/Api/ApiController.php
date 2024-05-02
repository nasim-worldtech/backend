<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
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
        $validator = Validator::make($request->all(), [
            "email" => "required|email|string",
            "password" => "required|min:6",
        ]);

        if ($validator->fails()) {
            return $this->responseJson(400, false, 'Validation Errors', $validator->errors());
        }

        $login_history = LoginHistory::create([
            'email' => $request->email,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_for' => 'Login',
        ]);
        $user = User::where("email", $request->email)->first();

        if (empty($user)) {
            // User not found
            $login_history->remark = 'No Data';
            $login_history->status = 'Failed';
            $login_history->save();

            return $this->responseJson(401, false, "Invalid Login Credentials");
        }

        if (!Hash::check($request->password, $user->password)) {
            $login_history->user_id = $user->id;
            $login_history->remark = 'Incorrect Password';
            $login_history->status = 'Failed';
            $login_history->save();

            return $this->responseJson(401, false, "Incorrect Password");
        }

        $login_history->remark = 'Login';
        $login_history->status = 'Success';
        $login_history->save();

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

    public function logout(Request $request)
    {
        $user = auth()->user();

        try {
            LoginHistory::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'request_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_for' => 'Logout',
                'status' => 'Success',
            ]);

            $token = $user->token();
            if ($token) {
                $token->revoke();
            }
        } catch (\Throwable $e) {
            return $this->responseJson(500, false, "Logout Failed", ['error' => $e->getMessage()]);
        }

        return $this->responseJson(200, true, "User Logout Successfully");
    }
}
