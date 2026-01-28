<?php

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 商品コードから商品情報を取得する
     * 
     * @param string $code
     * @return array|null 商品情報配列、または見つからない場合はnull
     */
    public function getByCode($code) {
        $stmt = $this->pdo->prepare("SELECT * FROM M_商品マスタ WHERE 商品コード = :code");
        $stmt->execute([':code' => $code]);
        $result = $stmt->fetch();

        if ($result) {
            return [
                'id' => $result['商品ID'],
                'code' => $result['商品コード'],
                'name' => $result['商品名'],
                'price' => 0, // マスタに価格がないため0とする
                'category_id' => $result['カテゴリ']
            ];
        }

        return null;
    }
}
