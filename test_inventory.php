<?php
require_once __DIR__ . '/classes/Product.php';
require_once __DIR__ . '/classes/Inventory.php';

echo "=== Backend Verification ===\n";

// 1. 商品検索テスト
echo "\n[Test 1] Product Search\n";
$productModel = new Product();
$code = '4901330574369'; // テスト用JANコード
$product = $productModel->getByCode($code);

if ($product) {
    echo "OK: Product Found: " . $product['name'] . " (ID: " . $product['id'] . ")\n";
    $productId = $product['id'];
} else {
    echo "FAIL: Product Not Found for code $code.\n";
    exit;
}

// 2. 理論在庫テスト
echo "\n[Test 2] Theoretical Stock Calculation\n";
$inventoryModel = new Inventory();
$theoreticalStock = $inventoryModel->getTheoreticalStock($productId);
echo "OK: Theoretical Stock: $theoreticalStock\n";

// 3. 在庫登録テスト
echo "\n[Test 3] Inventory Registration\n";
try {
    $actualStock = $theoreticalStock + 2; // +2の差異を作る
    $inventoryId = $inventoryModel->registerInventory($productId, 1, 1, $actualStock);
    echo "OK: Registered Inventory ID: $inventoryId (Actual: $actualStock, Diff: +2)\n";
} catch (Exception $e) {
    echo "FAIL: Registration Error: " . $e->getMessage() . "\n";
}

echo "\nVerification Complete.\n";
