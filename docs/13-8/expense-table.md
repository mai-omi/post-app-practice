テーブル定義書（解答例）

users（社員）

カラム名 データ型 制約 説明
id BIGINT PRIMARY KEY, AUTO_INCREMENT 社員を1人ずつ区別する番号
name VARCHAR(50) NOT NULL 社員の名前
email VARCHAR(255) NOT NULL, UNIQUE ログインに使うメールアドレス
password VARCHAR(255) NOT NULL ログインに使うパスワード
is_approver BOOLEAN NOT NULL, DEFAULT false 承認者として扱うかどうか
created_at TIMESTAMP - 登録した日時
updated_at TIMESTAMP - 最後に書き換えた日時
expenses（経費の申請）

カラム名 データ型 制約 説明
id BIGINT PRIMARY KEY, AUTO_INCREMENT 申請を1件ずつ区別する番号
user_id BIGINT NOT NULL, FOREIGN KEY 申請を出した社員
spent_on DATE NOT NULL 立て替えた日
amount INT NOT NULL 金額（円）
category ENUM('transport', 'book', 'meeting', 'other') NOT NULL 費目
purpose VARCHAR(100) NOT NULL 何に使ったか
status ENUM('applied', 'approved', 'rejected') NOT NULL 申請の状態
reject_reason VARCHAR(200) - 差し戻したときの理由
approver_id BIGINT FOREIGN KEY 承認または差し戻しをした社員
decided_at DATETIME - 承認または差し戻しをした日時
created_at TIMESTAMP - 登録した日時
updated_at TIMESTAMP - 最後に書き換えた日時
