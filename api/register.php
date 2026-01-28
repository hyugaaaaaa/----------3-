<?php
header('Content-Type: application/json; charset=utf-8');

// DB接続設定とクラス定義の読み込み
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../classes/Inventory.php';

try {
    // POSTリクエストのみ許可
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid Request Method');
    }

    // 入力データの取得
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['product_id']) || !isset($input['actual_stock'])) {
        throw new Exception('必要なパラメータ(product_id, actual_stock)が不足しています。');
    }

    $productId = (int)$input['product_id'];
    $actualStock = (int)$input['actual_stock'];

    // インスタンス化
    $inventoryModel = new Inventory($pdo);

    // 登録処理
    // 倉庫ID, 棚ID は一旦デフォルト(1)とします
    $result = $inventoryModel->registerInventory($productId, $actualStock);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => '在庫を登録しました'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('登録に失敗しました');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
