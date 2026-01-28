<?php

class Inventory {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 商品の現在の理論在庫数を計算する
     * T_入出庫履歴 を集計して算出
     * 区分: 1=入庫(+), 2=出庫(-) と仮定
     * 
     * @param int $productId
     * @return int 現在庫数
     */
    public function getTheoreticalStock($productId) {
        // 全期間の入出庫履歴を集計
        // 削除フラグが0(有効)なもののみを対象
        $sql = "
            SELECT 
                SUM(CASE 
                    WHEN 区分 = 1 THEN 数量 
                    WHEN 区分 = 2 THEN -数量 
                    ELSE 0 
                END) as current_stock
            FROM T_入出庫履歴
            WHERE 商品ID = :product_id
            AND (削除フラグ IS NULL OR 削除フラグ = 0)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        $result = $stmt->fetch();

        return $result['current_stock'] !== null ? (int)$result['current_stock'] : 0;
    }

    /**
     * 棚卸（実在庫）データを登録する
     * 
     * @param int $productId
     * @param int $actualStock 実在庫数
     * @param int $warehouseId 倉庫ID (デフォルト1)
     * @param int $shelfId 棚ID (デフォルト1)
     * @return bool 成功時true
     */
    public function registerInventory($productId, $actualStock, $warehouseId = 1, $shelfId = 1) {
        try {
            $this->pdo->beginTransaction();

            $theoreticalStock = $this->getTheoreticalStock($productId);
            $diff = $actualStock - $theoreticalStock;
            $now = date('Y-m-d H:i:s');
            $today = date('Y-m-d');
            $currentMonth = date('Ym');

            // T_棚卸履歴 にINSERT
            $sql = "
                INSERT INTO T_棚卸履歴 (
                    年月, 棚卸日, 倉庫ID, 棚ID, 商品ID, 
                    理論在庫, 実在庫, 差異, 
                    確定フラグ, 確定日時, 
                    登録者, 登録日時, 更新者, 更新日時
                ) VALUES (
                    :ym, :date, :warehouse_id, :shelf_id, :product_id,
                    :theoretical, :actual, :diff,
                    1, :confirmed_at,
                    99, :created_at, 99, :updated_at
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':ym' => $currentMonth,
                ':date' => $today,
                ':warehouse_id' => $warehouseId,
                ':shelf_id' => $shelfId,
                ':product_id' => $productId,
                ':theoretical' => $theoreticalStock,
                ':actual' => $actualStock,
                ':diff' => $diff,
                ':confirmed_at' => $now,
                ':created_at' => $now,
                ':updated_at' => $now
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
