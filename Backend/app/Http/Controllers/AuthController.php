<?php

namespace App\Http\Controllers;

use App\Services\AuthServiceInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Routing\Controller;


// controller handles incoming requests and sends responses
// controller validate data and calls the service layer for business logic
class AuthController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService; 
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh', 'logout']]);
    }

    public function register(RegisterRequest $request) //full name. .... 
    {
        $validatedData = $request->validated(); // Get validated data
        $result = $this->authService->register($validatedData);

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        $statusCode = $result['status'] === 'error' ? 401 : 200;
        
        return response()->json($result, $statusCode);
    }

    public function logout()
    {
        $result = $this->authService->logout();
        
        return response()->json($result);
    }

    public function refresh()
    {
        $result = $this->authService->refresh();
        
        return response()->json($result);
    }
    
}