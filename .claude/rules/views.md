---
paths:
  - resources/views/**
---

# Blade Rules

## フォームのセキュリティ

- POST フォームには必ず `@csrf` を入れる
- PUT・PATCH・DELETE は `@method('PUT')` などのスプーフィングを使う

```blade
<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')
    ...
</form>
```

## フォームの name 属性

- `name` 属性は DB のカラム名に揃える（Controller の `validate()` キーと一致させる）

## 出力のエスケープ

- ユーザー入力を出力するときは `{{ }}` を使う（`{!! !!}` は使わない）

## エラー表示

- バリデーションエラーは `@error` ディレクティブで各フィールドの直下に表示する

```blade
<input type="text" name="title" value="{{ old('title') }}">
@error('title')
    <p>{{ $message }}</p>
@enderror
```
