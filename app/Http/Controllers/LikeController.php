<?php

namespace App\Http\Controllers;

use App\Models\Post;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $userId = auth()->id();

        $deleted = $post->likes()->where('user_id', $userId)->delete();

        if (!$deleted) {
            try {
                $post->likes()->create(['user_id' => $userId]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                // 同時リクエストによる競合 — すでにいいね済みとして扱う
            }
        }

        return redirect()->route('posts.index');
    }
}
