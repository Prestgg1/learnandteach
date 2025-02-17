<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = User::where("email", $credentials["email"])->first();
            $userToken = $user->createToken("api-token")->plainTextToken;

            return [
                "message" => "Login successful",
                "token" => $userToken,
                "status" => "success",
                "user" => $user,
            ];
        }
        return [
            "status" => "error",
            "message" => "Invalid credentials",
        ];
    }

    public function register(array $userData)
    {
        $user = User::create([
            "name" => $userData["name"],
            "email" => $userData["email"],
            "password" => Hash::make($userData["password"]), // Şifreyi hash'le
        ]);

        Auth::login($user);
        $userToken = $user->createToken("api-token")->plainTextToken;

        return [
            "message" => "User created successfully",
            "user" => $user,
            "token" => $userToken,
            "status" => "success",
        ];
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return [
                "message" => "Logged out successfully",
                "status" => "success",
            ];
        }
        return [
            "message" => "User not authenticated",
            "status" => "error",
        ];
    }

    public function checkEmail(string $email)
    {
        if (User::where("email", $email)->exists()) {
            return [
                "status" => "error",
                "message" => "Bu email qeydiyatda var.",
            ];
        }
        return [
            "status" => "true",
        ];
    }
}
