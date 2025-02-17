<?php

use App\Events\LikeUpdated;
use Illuminate\Support\Facades\Route;

Route::get("/csrf-token", function () {
    return response()->json(["csrf_token" => csrf_token()]);
});

Route::get("/", function () {
    return "Hi everybody. I'm Prestgg";
});
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::post("/sanctum/token", function (Request $request) {
    $request->validate([
        "email" => "required|email",
        "password" => "required",
        "device_name" => "required",
    ]);

    $user = User::where("email", $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            "email" => ["The provided credentials are incorrect."],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::get("/like", function () {
    event(new LikeUpdated(Post::first()));
});
