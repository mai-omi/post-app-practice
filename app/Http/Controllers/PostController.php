<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category'])->latest()->get();

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('id')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'title'       => 'required|string|max:255',
                'content'     => 'required|string|max:140',
                'category_id' => 'required|exists:categories,id',
            ],
            [
                'title.required'       => 'タイトルは必須です。',
                'title.max'            => 'タイトルは255字以内で入力してください。',
                'content.required'     => '本文は必須です。',
                'content.max'          => '本文は140字以内で入力してください。',
                'category_id.required' => 'トピックを選択してください。',
                'category_id.exists'   => '有効なトピックを選択してください。',
            ]
        );

        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect()->route('posts.index');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::orderBy('id')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate(
            [
                'title'       => 'required|string|max:255',
                'content'     => 'required|string|max:140',
                'category_id' => 'required|exists:categories,id',
            ],
            [
                'title.required'       => 'タイトルは必須です。',
                'title.max'            => 'タイトルは255字以内で入力してください。',
                'content.required'     => '本文は必須です。',
                'content.max'          => '本文は140字以内で入力してください。',
                'category_id.required' => 'トピックを選択してください。',
                'category_id.exists'   => '有効なトピックを選択してください。',
            ]
        );

        $post->update($validated);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index');
    }
}
