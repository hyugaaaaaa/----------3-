<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Inventory.php';

try {
    // POSTメソッドのみ許可
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid Request Method');
    }

    // JSONデータの受け取り
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['product_id']) || !isset($input['actual_stock'])) {
        throw new Exception('Required parameters are missing.');
    }

    $productId = (int)$input['product_id'];
    $actualStock = (int)$input['actual_stock'];
    
    // 倉庫・棚は今回は固定値 (またはパラメータで受け取るが、UI簡易化のため固定とする)
    $warehouseId = 1; // デフォルト倉庫
    $shelfId = 1;     // デフォルト棚

    $inventoryModel = new Inventory();
    
    // 登録実行
    $inventoryId = $inventoryModel->registerInventory($productId, $warehouseId, $shelfId, $actualStock);

    echo json_encode([
        'success' => true,
        'message' => '在庫登録が完了しました',
        'data' => [
            'inventory_id' => $inventoryId
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
