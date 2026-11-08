<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user     = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->post     = Post::factory()->create([
            'user_id'     => User::factory()->create()->id,
            'category_id' => $this->category->id,
        ]);
    }

    // 受け入れ条件1: ログインユーザーがいいねボタンを押すといいねが登録される
    public function test_ログインユーザーがいいねを押すといいねが登録される(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('posts.like', $this->post));

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }

    // 受け入れ条件2: いいね済みのポストをもう一度押すといいねが取り消される
    public function test_いいね済みのポストを再度押すといいねが取り消される(): void
    {
        $this->post->likes()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->post(route('posts.like', $this->post));

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }

    // 受け入れ条件3: タイムラインにいいね数が表示される
    public function test_タイムラインにいいね数が表示される(): void
    {
        $this->post->likes()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee('♥ 1');
    }

    // 受け入れ条件4: 未認証ユーザーがいいねを押すとログインページへリダイレクトされる
    public function test_未認証ユーザーがいいねを押すとログインページへリダイレクトされる(): void
    {
        $response = $this->post(route('posts.like', $this->post));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('likes', 0);
    }
}
