<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterCheckRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\ResponseService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->all());
        return response()->json($result, $result["status"] === "success" ? 200 : 401);
    }

    public function checkEmail(RegisterCheckRequest $request)
    {
        $result = $this->authService->checkEmail($request->email);
        return ResponseService::success($result, "User created successfully");
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->all());
        return ResponseService::success($result, "User created successfully");
    }

    public function logout()
    {
        return response()->json($this->authService->logout(), 200);
    }
}
