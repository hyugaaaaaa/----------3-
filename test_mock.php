<?php
require_once __DIR__ . '/classes/Product.php';
require_once __DIR__ . '/classes/ScanLogger.php';

echo "Testing Product Model:\n";
$productModel = new Product();

// Test Case 1: Existing Product
$code = '4901330574369';
$product = $productModel->getByCode($code);
echo "Code: $code -> " . ($product ? $product['name'] : 'Not Found') . "\n";

// Test Case 2: Non-Existing Product
$code = '9999999999999';
$product = $productModel->getByCode($code);
echo "Code: $code -> " . ($product ? $product['name'] : 'Not Found') . "\n";

echo "\nTesting ScanLogger:\n";
$logger = new ScanLogger();
$logger->log('TEST_CODE', 'Test Log Message');
echo "Log executed. Check scan_debug.log.\n";
