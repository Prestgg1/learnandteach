<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\isAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/user', function (Request $request) {
    return $request->user() 
    ;
  });
  Route::put('/profile/update', [UserController::class, 'updateProfileDetails'])->name('profile.update');
  Route::put('/profile/update', [UserController::class, 'uploadProfileImage'])->name('profile.update');
  
  Route::get('users', function (Request $request) {
    return User::all();
  });
  Route::get('/user/profile', function () {
    // Uses first & second middleware...
  });
});
Route::prefix('auth')->group(function () {
  Route::post("login", [AuthController::class, 'login'])->name('login');
  Route::post('register', [AuthController::class, 'register'])->name('register');
});


Route::get("posts", [PostController::class, 'index'])->name('posts');
Route::get("post/{id}", [PostController::class, 'show'])->name('post');

Route::middleware([isAdminMiddleware::class])->group(function () {
  Route::put("posts/{id}", [PostController::class, 'update'])->name('update');
  Route::delete("posts/{id}", [PostController::class, 'destroy'])->name('destroy');
  Route::post("posts", [PostController::class, 'store'])->name('store');
});



