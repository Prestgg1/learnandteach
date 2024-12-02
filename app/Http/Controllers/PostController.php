<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;



class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Bütün Postları Getir",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Postlar uğurla getirildi"
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::join('users', 'posts.author', '=', 'users.id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Melumatlar Uğurla Çıxarıldı',
            'data' => $posts
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{slug}",
     *     summary="Tək bir postu getir",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Postun slug değeri",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post uğurla getirildi"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post bulunamadı"
     *     )
     * )
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return response()->json([
            'status' => true,
            'message' => 'Post uğurla bulundu',
            'data' => $post
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Yeni bir post oluştur",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Post Başlığı"),
     *             @OA\Property(property="email", type="string", example="email@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post başarıyla oluşturuldu"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Doğrulama hatası"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:customers|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = Post::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Post başarıyla oluşturuldu',
            'data' => $post
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Mevcut bir postu güncelle",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Postun ID'si",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Yeni Başlık"),
     *             @OA\Property(property="email", type="string", example="newemail@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post başarıyla güncellendi"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Doğrulama hatası"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Post başarıyla güncellendi',
            'data' => $post
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Bir postu sil",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Postun ID'si",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Post başarıyla silindi"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post bulunamadı"
     *     )
     * )
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post başarıyla silindi'
        ], 204);
    }
}
