<?php

namespace App\Http\Controllers;

use App\ApiResponses;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  use ApiResponses;
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User Login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1N..."),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The provided credentials are incorrect."),
     *             @OA\Property(property="status", type="string", example="error")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('The provided credentials are incorrect.', 401);
        }
        $userToken = $user->createToken('api-token')->plainTextToken;
        return $this->ok(['token' => $userToken, 'user' => $user]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User Registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1N..."),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password must be at least 8 characters."))
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        Auth::login($user);

        $userToken = $user->createToken('api-token')->plainTextToken;

        return $this->ok(['token' => $userToken, 'user' => $user]);
    }
    /**
 * @OA\Post(
 *     path="/api/auth/logout",
 *     summary="User logout",
 *     tags={"Authentication"},
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Uğurla Çıxış Edildi"),
 *             @OA\Property(property="status", type="string", example="success")
 *         )
 *     )
 * )
 */

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->ok('Uğurla Çıxış Edildi');
    }

/**
 * @OA\Post(
 *     path="/api/auth/forget-password",
 *     summary="Send password reset link",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email"},
 *             @OA\Property(property="email", type="string", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset link sent successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Şifre sıfırlama bağlantısı gönderildi."),
 *             @OA\Property(property="status", type="string", example="success")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error while sending reset link",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Bir hata oluştu. Lütfen daha sonra tekrar deneyin."),
 *             @OA\Property(property="status", type="string", example="error")
 *         )
 *     )
 * )
 */


    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        $status = Password::sendResetLink($request->only('email'));
    
        if ($status == Password::RESET_LINK_SENT) {
            return response([
                'message' => 'Şifre sıfırlama bağlantısı gönderildi.',
                'status' => 'success'
            ]);
        }
    
        return response([
            'message' => 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.',
            'status' => 'error'
        ], 500);
    }
    /**
 * @OA\Post(
 *     path="/api/auth/reset-password",
 *     summary="Reset password",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email", "token", "password", "password_confirmation"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="token", type="string", example="reset-token-example"),
 *             @OA\Property(property="password", type="string", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sifreniz yenilendi"),
 *             @OA\Property(property="status", type="string", example="success")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid token",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Yanlış token"),
 *             @OA\Property(property="status", type="string", example="error")
 *         )
 *     )
 * )
 */

    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
   
        $status = Password::reset(
          $request->only('email', 'password', 'password_confirmation', 'token'),
          function (User $user, string $password) {
              $user->forceFill([
                  'password' => Hash::make($password)
              ])->setRememberToken(Str::random(60));
   
              $user->save();
   
              event(new PasswordReset($user));
          }
      );
   
      return $status === Password::PASSWORD_RESET
                  ? redirect()->route('login')->with('status', __($status))
                  : back()->withErrors(['email' => [__($status)]]);
   
        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message' => 'Sifreniz yenilendi',
                'status' => 'success'
            ], 200);
        } else {
            return response([
                'message' => 'Yanlış token',
                'status' => 'error'
            ], 401);
        }
    }
}
