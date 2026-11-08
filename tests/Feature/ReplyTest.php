<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ 表示

    public function test_ログイン済みユーザーがポスト詳細画面を開ける(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->content);
    }

    public function test_リプライが詳細画面に表示される(): void
    {
        $user  = User::factory()->create();
        $post  = Post::factory()->create();
        $reply = Reply::factory()->create(['post_id' => $post->id]);

        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertSee($reply->content);
    }

    public function test_自分のリプライには削除ボタンが表示される(): void
    {
        $user  = User::factory()->create();
        $post  = Post::factory()->create();
        $reply = Reply::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertSee(route('replies.destroy', $reply));
    }

    public function test_他人のリプライには削除ボタンが表示されない(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $post  = Post::factory()->create();
        $reply = Reply::factory()->create(['post_id' => $post->id, 'user_id' => $other->id]);

        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertDontSee(route('replies.destroy', $reply));
    }

    // ------------------------------------------------------------------ 認証

    public function test_未ログインでポスト詳細画面にアクセスするとリダイレクトされる(): void
    {
        $post = Post::factory()->create();

        $this->get(route('posts.show', $post))
            ->assertRedirect(route('login'));
    }

    public function test_未ログインでリプライ投稿するとリダイレクトされる(): void
    {
        $post = Post::factory()->create();

        $this->post(route('replies.store', $post), ['content' => 'テスト'])
            ->assertRedirect(route('login'));
    }

    public function test_未ログインでリプライ削除しようとするとリダイレクトされる(): void
    {
        $reply = Reply::factory()->create();

        $this->delete(route('replies.destroy', $reply))
            ->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------ リプライ作成

    public function test_正常なリプライが保存されて詳細画面にリダイレクトされる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('replies.store', $post), ['content' => 'こんにちは'])
            ->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('replies', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'こんにちは',
        ]);
    }

    public function test_140字のリプライは保存される(): void
    {
        $user    = User::factory()->create();
        $post    = Post::factory()->create();
        $content = str_repeat('あ', 140);

        $this->actingAs($user)
            ->post(route('replies.store', $post), ['content' => $content])
            ->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('replies', ['content' => $content]);
    }

    public function test_141字のリプライはエラーになり保存されない(): void
    {
        $user    = User::factory()->create();
        $post    = Post::factory()->create();
        $content = str_repeat('あ', 141);

        $this->actingAs($user)
            ->post(route('replies.store', $post), ['content' => $content])
            ->assertSessionHasErrors(['content']);

        $this->assertDatabaseMissing('replies', ['content' => $content]);
    }

    public function test_本文が空のリプライはエラーになり保存されない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('replies.store', $post), ['content' => ''])
            ->assertSessionHasErrors(['content']);

        $this->assertDatabaseCount('replies', 0);
    }

    // ------------------------------------------------------------------ リプライ削除

    public function test_自分のリプライを削除できる(): void
    {
        $user  = User::factory()->create();
        $post  = Post::factory()->create();
        $reply = Reply::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('replies.destroy', $reply))
            ->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    public function test_他人のリプライを削除しようとすると403になる(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $post  = Post::factory()->create();
        $reply = Reply::factory()->create(['post_id' => $post->id, 'user_id' => $other->id]);

        $this->actingAs($user)
            ->delete(route('replies.destroy', $reply))
            ->assertForbidden();

        $this->assertDatabaseHas('replies', ['id' => $reply->id]);
    }
}
