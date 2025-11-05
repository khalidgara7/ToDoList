<?php

namespace App\Services;

use App\Models\User;

interface AuthServiceInterface
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array;

    /**
     * Login user with credentials
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array;

    /**
     * Logout current user
     *
     * @return array
     */
    public function logout(): array;

    /**
     * Refresh user token
     *
     * @return array
     */
    public function refresh(): array;
}