<?php
require_once __DIR__ . '/../db_config.php';

class Product {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    /**
     * 商品コードから商品情報を取得する
     * 
     * @param string $code
     * @return array|null 商品情報配列、または見つからない場合はnull
     */
    public function getByCode($code) {
        // M_商品マスタ から検索
        // NOTE: 価格カラムが存在しないため、仮に 0 を返すか、あるいは別テーブル参照などの対応が必要。
        // ここでは一旦 0 とし、UI側で制御する方針とする。
        $sql = "SELECT * FROM `M_商品マスタ` WHERE `商品コード` = :code LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
        
        $product = $stmt->fetch();
        
        if ($product) {
            return [
                'id' => $product['商品ID'],
                'code' => $product['商品コード'],
                'name' => $product['商品名'],
                'price' => 0, // マスタにないため
                'category' => $product['カテゴリ']
            ];
        }

        return null;
    }
}
