<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserService
{
    /**
     * Handle user login and return Sanctum API token.
     *
     * @param array $credentials
     * @return string|null
     */
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user = User::find($user->id);
            return $user->createToken('auth-token')->plainTextToken;
        }

        return null;
    }

    /**
     * Register a new user and return Sanctum API token.
     *
     * @param array $data
     * @return string|null
     */
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user->createToken('auth-token')->plainTextToken;
    }
}