<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\updateProfileDetailsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updateProfileDetails(updateProfileDetailsRequest $request)
    {
        $request->validated();
        $user = Auth::user();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->about = $request->about;
        $user->save();
        return response()->json([
            "message" => "Profil Məlumatları uğurla dəyiştirildi.",
            "user" => $user,
        ]);
    }

    public function profile()
    {
        return Auth::user();
    }

    public function uploadProfileImage(ProfileImageRequest $request)
    {
        $request->validated();

        $user = Auth::user();
        $user->profile_image = $request->profile_image;

        // Veritabanında güncelle
        $user->save();

        return response()->json([
            "message" => "Profil Şəkli Uğurla Yükləndi.",
        ]);
    }
}
