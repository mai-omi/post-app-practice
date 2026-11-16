---
paths:
  - app/Http/Controllers/**
  - routes/**
---

# HTTP Rules

## バリデーション

- バリデーションは `$request->validate()` または FormRequest で書く
- エラーメッセージはすべて日本語で書く
- テキスト入力フィールドの文字数上限は `max:140`

```php
$request->validate([
    'content' => ['required', 'string', 'max:140'],
], [
    'content.required' => '本文を入力してください。',
    'content.max'      => '本文は140字以内で入力してください。',
]);
```

## 認可

- `authorize()` または `$this->authorize()` はバリデーションより前に呼ぶ
- 作成系（store）は `auth` ミドルウェアで十分な場合は Policy 不要

## ルート

- 認証が必要なルートはすべて `Route::middleware('auth')->group()` 内に置く
- すべてのルートに `->name()` を付ける

```php
Route::middleware('auth')->group(function () {
    Route::post('/posts/{post}/replies', [ReplyController::class, 'store'])
        ->name('replies.store');
});
```

## コントローラの責務

- DB 操作はモデルに委譲し、コントローラはリクエスト→レスポンスの流れだけを書く
- `user_id` などはコントローラで `auth()->id()` をセットする（フォームから受け取らない）
