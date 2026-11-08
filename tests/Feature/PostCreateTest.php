<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCreateTest extends TestCase
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

    // 受け入れ条件1: タイムラインに「新規作成」ボタンが表示される
    public function test_タイムラインに新規作成ボタンが表示される(): void
    {
        $response = $this->actingAs($this->user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee(route('posts.create'));
    }

    // 受け入れ条件2 / テスト2: 未認証で /posts/create を開くとログイン画面へリダイレクト
    public function test_未認証でcreateを開くとログイン画面へリダイレクト(): void
    {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect(route('login'));
    }

    // 未認証で POST /posts もリダイレクト
    public function test_未認証でPOSTするとログイン画面へリダイレクト(): void
    {
        $response = $this->post(route('posts.store'), [
            'title'       => 'テスト',
            'content'     => '本文',
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('posts', 0);
    }

    // 受け入れ条件3: 全項目入力して送信するとタイムライン先頭にポストが追加される
    public function test_全項目入力して送信するとタイムラインに追加される(): void
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'title'       => 'テストタイトル',
            'content'     => 'テスト本文',
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', [
            'title'       => 'テストタイトル',
            'content'     => 'テスト本文',
            'category_id' => $this->category->id,
            'user_id'     => $this->user->id,
        ]);
    }

    // 受け入れ条件4: 作成したポストに自分の名前と編集・削除ボタンが表示される
    public function test_作成したポストに編集削除ボタンが表示される(): void
    {
        Post::factory()->create([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'title'       => '自分のポスト',
            'content'     => '本文',
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('編集');
        $response->assertSee('削除');
    }

    // 受け入れ条件5: 本文が空だとエラーになりポストは増えない
    public function test_本文が空だとエラーになりポストは増えない(): void
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'title'       => 'タイトル',
            'content'     => '',
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseCount('posts', 0);
    }

    // テスト5: タイトルが空だとエラーになりポストは増えない
    public function test_タイトルが空だとエラーになりポストは増えない(): void
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'title'       => '',
            'content'     => '本文',
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('posts', 0);
    }

    // テスト6: トピックが未選択だとエラーになりポストは増えない
    public function test_トピックが未選択だとエラーになりポストは増えない(): void
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'title'   => 'タイトル',
            'content' => '本文',
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('posts', 0);
    }

    // テスト7: 本文141字でエラーメッセージが表示されポストは増えない
    public function test_本文141字だとエラーメッセージが表示されポストは増えない(): void
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'title'       => 'タイトル',
            'content'     => str_repeat('あ', 141),
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors(['content' => '本文は140字以内で入力してください。']);
        $this->assertDatabaseCount('posts', 0);
    }

    // テスト8: 他人の投稿には編集・削除ボタンが出ない
    public function test_他人の投稿には編集削除ボタンが出ない(): void
    {
        $other = User::factory()->create();
        Post::factory()->create([
            'user_id'     => $other->id,
            'category_id' => $this->category->id,
            'title'       => '他人のポスト',
            'content'     => '本文',
        ]);

        $response = $this->actingAs($this->user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertDontSee('編集');
        $response->assertDontSee('削除');
    }
}
