<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate(
            ['content' => 'required|string|max:140'],
            [
                'content.required' => '本文を入力してください。',
                'content.max' => '本文は140字以内で入力してください。',
            ]
        );

        $post->replies()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('posts.show', $post);
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('delete', $reply);

        $post = $reply->post;
        $reply->delete();

        return redirect()->route('posts.show', $post);
    }
}
