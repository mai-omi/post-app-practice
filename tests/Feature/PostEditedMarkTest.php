<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostEditedMarkTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user     = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    // 受け入れ条件1: 一度も編集していないポストには「編集済み」が出ない
    public function test_未編集のポストにはタイムラインに編集済みが出ない(): void
    {
        Post::factory()->create([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertDontSee('編集済み');
    }

    // 受け入れ条件2: 編集して保存したポストにはタイムラインに「編集済み」が出る
    public function test_編集済みのポストにはタイムラインに編集済みが出る(): void
    {
        $post = Post::factory()->create([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'created_at'  => now()->subMinute(),
            'updated_at'  => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee('編集済み');
    }
}
