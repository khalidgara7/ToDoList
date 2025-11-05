<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get list of users for task assignment
     */
    public function index()
    {
        $currentUser = Auth::user();
        
        // Get all users except current user
        $users = User::select('id', 'full_name', 'email')
            ->where('id', '!=', $currentUser->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get current authenticated user profile
     */
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}