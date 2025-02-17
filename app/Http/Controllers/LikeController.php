<?php

// App\Http\Controllers\LikeController.php
namespace App\Http\Controllers;

use App\Events\LikeUpdated;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{

    public function checkLike(Post $post)
    {
        $isLiked = $post->likes()->where('user_id', auth()->id())->exists();
        
        return response()->json([
            'is_liked' => $isLiked
        ]);
    }


    public function toggleLike(Request $request,$postId)
    {
        
       

        $user = Auth::user();
    
        $like = Like::where('user_id', $user->id)->where('post_id', $postId)->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            $status = 'liked';
        }   
        $likeCount = Like::where('post_id', $postId)->count();

            broadcast(new LikeUpdated($postId,$status,$likeCount))->toOthers();
        } 


        }
    

