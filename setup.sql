-- データベース作成 (存在しない場合)
CREATE DATABASE IF NOT EXISTS posic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE posic_db;

-- 商品マスタテーブル作成
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'バーコード/QRコード値',
    name VARCHAR(255) NOT NULL COMMENT '商品名',
    price INT DEFAULT 0 COMMENT '価格',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- スキャン履歴テーブル作成
CREATE TABLE IF NOT EXISTS scan_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    result VARCHAR(50) COMMENT '照合結果 (例: Match, Not Found)',
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- テストデータ投入
-- すでにあるデータを消さないように IGNORE を使用
INSERT IGNORE INTO products (code, name, price) VALUES 
('4901330574369', 'ポテトチップス うすしお味', 150),
('4549131970258', 'ダイソー USBケーブル', 110),
('TEST12345', 'テスト商品A', 500);
