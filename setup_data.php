<?php
// setup_data.php
require_once __DIR__ . '/db_config.php';

try {
    $pdo = getPDO();
    echo "Connecting to database... OK\n";

    // 外部キー制約等の確認（今回はMyISAMかInnoDBか不明だが、念のため無効化してTruncate）
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // 対象テーブル
    $tables = [
        'M_汎用コードマスタ',
        'M_倉庫マスタ',
        'M_棚マスタ',
        'M_商品マスタ',
        'M_ユーザーマスタ',
        'T_月次在庫',
        'T_入出庫履歴',
        'T_棚卸履歴'
    ];

    foreach ($tables as $table) {
        // テーブル存在確認 (簡易)
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
            echo "Truncating $table... ";
            $pdo->exec("TRUNCATE TABLE `$table`");
            echo "Done.\n";
        } catch (PDOException $e) {
            echo "Skipping $table (Not found or error).\n";
        }
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 1. M_ユーザーマスタ
    echo "Inserting M_ユーザーマスタ... ";
    $sql = "INSERT INTO `M_ユーザーマスタ` (`ユーザID`, `ユーザ名`, `登録者`, `登録日時`, `更新者`, `更新日時`) VALUES
            (1, 'admin', 0, NOW(), 0, NOW()),
            (2, 'user', 1, NOW(), 1, NOW())";
    $pdo->exec($sql);
    echo "Done.\n";

    // 2. M_倉庫マスタ
    echo "Inserting M_倉庫マスタ... ";
    $sql = "INSERT INTO `M_倉庫マスタ` (`倉庫ID`, `倉庫コード`, `倉庫名`, `有効フラグ`, `登録者`, `登録日時`, `更新者`, `更新日時`) VALUES
            (1, 'WH01', '本社倉庫', 1, 1, NOW(), 1, NOW()),
            (2, 'WH02', '第2倉庫', 1, 1, NOW(), 1, NOW())";
    $pdo->exec($sql);
    echo "Done.\n";

    // 3. M_棚マスタ
    echo "Inserting M_棚マスタ... ";
    $sql = "INSERT INTO `M_棚マスタ` (`棚ID`, `倉庫ID`, `棚コード`, `棚名称`, `有効フラグ`, `登録者`, `登録日時`, `更新者`, `更新日時`) VALUES
            (1, 1, 'A-01', '棚A-01', 1, 1, NOW(), 1, NOW()),
            (2, 1, 'B-01', '棚B-01', 1, 1, NOW(), 1, NOW()),
            (3, 2, 'C-01', '棚C-01', 1, 1, NOW(), 1, NOW())";
    $pdo->exec($sql);
    echo "Done.\n";

    // 4. M_汎用コードマスタ (商品カテゴリ)
    echo "Inserting M_汎用コードマスタ... ";
    $sql = "INSERT INTO `M_汎用コードマスタ` (`カテゴリID`, `カテゴリ名`, `区分コード`, `区分名`, `登録ユーザID`, `登録日時`, `更新ユーザID`, `更新日時`) VALUES
            (1, '商品カテゴリ', 100, '食品', 1, NOW(), 1, NOW()),
            (1, '商品カテゴリ', 200, '雑貨', 1, NOW(), 1, NOW()),
            (1, '商品カテゴリ', 300, '電化製品', 1, NOW(), 1, NOW())";
    $pdo->exec($sql);
    echo "Done.\n";

    // 5. M_商品マスタ
    echo "Inserting M_商品マスタ... ";
    // ID, Code, Name, Category, Created...
    $sql = "INSERT INTO `M_商品マスタ` (`商品ID`, `商品コード`, `商品名`, `カテゴリ`, `登録者`, `登録日時`, `更新者`, `更新日時`) VALUES
            (1, '4901330574369', 'ポテトチップス うすしお味', 100, 1, NOW(), 1, NOW()),
            (2, '4549131970258', 'ダイソー USBケーブル', 300, 1, NOW(), 1, NOW()),
            (3, 'TEST001', 'テスト用商品A', 200, 1, NOW(), 1, NOW())";
    $pdo->exec($sql);
    echo "Done.\n";

    // 6. T_入出庫履歴 (初期在庫)
    echo "Inserting T_入出庫履歴... ";
    // 区分: 1=入庫, 2=出庫
    $sql = "INSERT INTO `T_入出庫履歴` (`履歴ID`, `対象年月`, `倉庫ID`, `棚ID`, `商品ID`, `区分`, `数量`, `備考`, `削除フラグ`, `登録者`, `登録日時`, `更新者`, `更新日時`) VALUES
            -- ポテトチップス: 入庫 100
            (1, 202601, 1, 1, 1, 1, 100, '初期在庫', 0, 1, NOW(), 1, NOW()),
            -- USBケーブル: 入庫 50
            (2, 202601, 1, 2, 2, 1, 50, '初期在庫', 0, 1, NOW(), 1, NOW()),
            -- テスト用商品A: 入庫 10 - 出庫 2 = 8
            (3, 202601, 2, 3, 3, 1, 10, '仕入', 0, 1, NOW(), 1, NOW()),
            (4, 202601, 2, 3, 3, 2, 2, '出荷', 0, 1, NOW(), 1, NOW())";
    $pdo->exec($sql);
    echo "Done.\n";

    echo "\nSample data created successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
