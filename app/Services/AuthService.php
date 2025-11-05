<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class AuthService implements AuthServiceInterface
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        // Handle image upload if exists
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('users', 'public');
        }

        // Create user through repository
        $user = $this->userRepository->create($data);

        // Generate JWT token
        $token = Auth::guard('api')->login($user);

        return [
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    /**
     * Login user with credentials
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized',
            ];
        }

        $user = Auth::guard('api')->user();

        return [
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ];
    }

    /**
     * Logout current user
     *
     * @return array
     */
    public function logout(): array
    {
        Auth::guard('api')->logout();

        return [
            'status' => 'success',
            'message' => 'Successfully logged out',
        ];
    }

    /**
     * Refresh user token
     *
     * @return array
     */
    public function refresh(): array
    {
        $user = Auth::guard('api')->user();
        
        return [
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'type' => 'bearer',
            ],
        ];
    }
}