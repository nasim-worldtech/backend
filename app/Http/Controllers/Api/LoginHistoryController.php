<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Traits\ApiResponse;

class LoginHistoryController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $loginHistories = LoginHistory::orderByDesc('request_at')->get();
        return $this->responseJson(200, true, 'Login History Retrieved', $loginHistories);
    }
}
