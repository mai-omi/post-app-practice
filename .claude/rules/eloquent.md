---
paths:
  - app/Models/**
---

# Eloquent Rules

## $fillable

- モデルには必ず `$fillable` を定義する
- フォームから受け取るフィールドだけを列挙する（`user_id` などシステムがセットするものは含めない）

## リレーション

- 戻り値の型を必ず書く

```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

## クエリ

- 一覧取得には `with()` で必要なリレーションを Eager Loading する（N+1 防止）

```php
// OK
Post::with(['user', 'category'])->latest()->get();

// NG
Post::latest()->get(); // ループ内で $post->user->name を呼ぶと N+1 になる
```
