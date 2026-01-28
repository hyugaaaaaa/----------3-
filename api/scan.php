<?php
header('Content-Type: application/json; charset=utf-8');

// クラスの読み込み
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ScanLogger.php';
require_once __DIR__ . '/../classes/Inventory.php';

// メイン処理の実行
try {
    // GETパラメータからコードを取得
    if (!isset($_GET['code']) || empty($_GET['code'])) {
        throw new Exception('コードが指定されていません。');
    }

    $code = htmlspecialchars($_GET['code']);

    // インスタンス化
    $productModel = new Product();
    $inventoryModel = new Inventory();
    $logger = new ScanLogger();

    // 商品検索
    $product = $productModel->getByCode($code);

    if ($product) {
        // 理論在庫の取得
        $stock = $inventoryModel->getTheoreticalStock($product['id']);

        // 商品が見つかった場合
        $resultMessage = 'Match: ' . $product['name'] . ' (Stock: ' . $stock . ')';
        $logger->log($code, $resultMessage);

        echo json_encode([
            'success' => true,
            'message' => '商品が見つかりました',
            'data' => [
                'id' => $product['id'],
                'code' => $code,
                'name' => $product['name'],
                'price' => $product['price'], // 0
                'stock' => $stock
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
