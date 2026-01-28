<?php
// db_config.php

// データベース設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management');
define('DB_USER', 'root');
define('DB_PASS', '');

function getPDO() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // 本番環境では詳細なエラーを表示しない方が良いが、開発中は表示する
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Database Connection Error: ' . $e->getMessage()]);
        exit;
    }
}
