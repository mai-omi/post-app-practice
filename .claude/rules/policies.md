---
paths:
  - app/Policies/**
---

# Policy Rules

## メソッド名

Gate の ability 名に合わせる。

| ability | メソッド名 |
|---------|-----------|
| `update` | `update` |
| `delete` | `delete` |

## 戻り値

戻り値の型は `bool` にする。

```php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id;
}
```

## 登録

新しい Policy を作ったら `app/Providers/AuthServiceProvider.php` の `$policies` に登録する。

```php
protected $policies = [
    Post::class  => PostPolicy::class,
    Reply::class => ReplyPolicy::class,
];
```

## パターン

既存の `PostPolicy` のパターンに揃える。新機能のポリシーは `PostPolicy` を参照して同じ構造で書く。
