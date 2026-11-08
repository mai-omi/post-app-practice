<?php

namespace App\Http\Controllers;

use App\Models\Post;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $userId = auth()->id();

        $like = $post->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create(['user_id' => $userId]);
        }

        return redirect()->route('posts.index');
    }
}
