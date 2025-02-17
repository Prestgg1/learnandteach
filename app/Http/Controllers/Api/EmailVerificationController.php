<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
{
    $user = User::findOrFail($request->id);

    if ($user->email_verified_at) {
        return '';
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return redirect()->away('app://open'); // The deep link
}
}
