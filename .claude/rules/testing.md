# Testing Rules

## 実行コマンド

```bash
./vendor/bin/sail test
```

すべて Pass するまで修正してから次の作業に進む。

## テストの置き場

| 種別 | ディレクトリ |
|------|-------------|
| Feature テスト（HTTP・DB） | `tests/Feature/` |
| Unit テスト | `tests/Unit/` |

## 書き方の決まり

- `use RefreshDatabase;` を必ずつける（テスト間のDB汚染を防ぐ）
- `setUp()` でテスト共通の User・Category をファクトリで生成する
- テストメソッド名は日本語で受け入れ条件を表す（例: `test_本文が空だとエラーになりポストは増えない`）
- バリデーションエラーのアサーションは `assertSessionHasErrors` を使い、エラーメッセージ文字列まで検証する
- DB に保存されないことの確認は `assertDatabaseCount('posts', 0)` で行う
- 認証が必要な操作は `actingAs($this->user)` を使う
- 未認証アクセスのリダイレクト先は `route('login')` とする

## 完了報告への記載

テスト実行結果（件数・Pass/Fail）を必ず含める。例：

```
テスト結果: 8 passed (16 assertions) ✅
```
