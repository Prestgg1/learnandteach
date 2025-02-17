<?php

use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\isAdminMiddleware;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum"])->group(function () {
    Route::post("/posts/{post}/like", [
        LikeController::class,
        "toggleLike",
    ])->name("like");
    Route::get("/posts/{post}/check-like", [
        LikeController::class,
        "checkLike",
    ])->name("check-like");

    Route::get("/user", [UserController::class, "profile"])->name("profile");
    Route::put("/profile/update", [
        UserController::class,
        "updateProfileDetails",
    ])->name("profile.update");

    Route::get("users", function (Request $request) {
        return User::all();
    });
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
    Route::get("/user/profile", function () {});
});

Route::prefix("auth")->group(function () {
    Route::post("login", [AuthController::class, "login"])->name("login");
    Route::prefix("register")->group(function () {
        Route::post("step1", [AuthController::class, "checkEmail"])->name("CheckEmail");
        Route::post("create", [AuthController::class, "register"])->name("create");
    });
});



Route::get("posts", [PostController::class, "index"])->name("posts");
Route::get("post/{slug}", [PostController::class, "show"])->name("post");
Route::post("posts/search", [PostController::class, "search"])->name("search");
Route::middleware([isAdminMiddleware::class])->group(function () {
    Route::put("posts/{id}", [PostController::class, "update"])->name("update");
    Route::delete("posts/{id}", [PostController::class, "destroy"])->name(
        "destroy"
    );
    Route::post("posts", [PostController::class, "store"])->name("store");
});
Route::get("/email/verify/{id}/{hash}", [
    EmailVerificationController::class,
    "verify",
])
    ->middleware("signed")
    ->name("verification.verify");
