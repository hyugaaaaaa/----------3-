<?php
header('Content-Type: application/json; charset=utf-8');

// DB接続設定とクラス定義の読み込み
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Inventory.php';
require_once __DIR__ . '/../classes/ScanLogger.php';

// メイン処理の実行
try {
    // GETパラメータからコードを取得
    if (!isset($_GET['code']) || empty($_GET['code'])) {
        throw new Exception('コードが指定されていません。');
    }

    $code = htmlspecialchars($_GET['code']);

    // インスタンス化 (db_config.phpで生成された $pdo を使用)
    $productModel = new Product($pdo);
    $inventoryModel = new Inventory($pdo);
    $logger = new ScanLogger();

    // 商品検索
    $product = $productModel->getByCode($code);

    if ($product) {
        // 商品が見つかった場合、現在庫（理論在庫）を取得
        $currentStock = $inventoryModel->getTheoreticalStock($product['id']);

        // ログ出力
        $resultMessage = 'Match: ' . $product['name'];
        $logger->log($code, $resultMessage);

        echo json_encode([
            'success' => true,
            'message' => '商品が見つかりました',
            'data' => [
                'id' => $product['id'],
                'code' => $product['code'],
                'name' => $product['name'],
                'price' => $product['price'],
                'current_stock' => $currentStock
            ]
        ], JSON_UNESCAPED_UNICODE);

    } else {
        // 商品が見つからなかった場合
        $resultMessage = 'Not Found';
        $logger->log($code, $resultMessage);

        echo json_encode([
            'success' => false,
            'message' => '未登録の商品です',
            'data' => [
                'code' => $code
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    // エラー発生時
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
