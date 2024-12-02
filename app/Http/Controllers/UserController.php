<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\updateProfileDetailsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/profile/update",
     *     summary="Update profile details",
     *     tags={"User"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "last_name", "about"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="about", type="string", example="I am a web developer.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Profil Məlumatları uğurla dəyiştirildi."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="about", type="string", example="I am a web developer."),
     *                 @OA\Property(property="profile_image", type="string", example="profile_images/avatar.jpg")
     *             )
     *         )
     *     )
     * )
     */
    public function updateProfileDetails(updateProfileDetailsRequest $request){

        $request->validated();
        $user = Auth::user();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->about = $request->about;
        $user->save();

        return response()->json([
            'message' => 'Profil Məlumatları uğurla dəyiştirildi.',
            'user' => $user,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/image",
     *     summary="Upload profile image",
     *     tags={"User"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"profile_image"},
     *                 @OA\Property(
     *                     property="profile_image",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile image file to upload"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile image uploaded successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Profil Şəkli Uğurla Yükləndi."),
     *             @OA\Property(property="profile_image_url", type="string", example="/storage/profile_images/avatar.jpg")
     *         )
     *     )
     * )
     */
    public function uploadProfileImage(ProfileImageRequest $request)
    {
        $request->validated();

        $user = Auth::user();

        // Eski resmi sil
        if ($user->profile_image) {
            Storage::delete($user->profile_image);
        }

        // Yeni resmi kaydet
        $path = $request->file('profile_image')->store('profile_images');

        // Veritabanında güncelle
        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'message' => 'Profil Şəkli Uğurla Yükləndi.',
            'profile_image_url' => Storage::url($path),
        ]);
    }
}
