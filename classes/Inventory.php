<?php
require_once __DIR__ . '/../db_config.php';

class Inventory {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    /**
     * 商品の理論在庫を計算する
     * 
     * @param int $productId
     * @return int 現在の理論在庫数
     */
    public function getTheoreticalStock($productId) {
        // T_入出庫履歴 から計算
        // 区分が 1:入庫(+)、2:出庫(-) と仮定
        // 棚卸履歴などがある場合はそこからの差分計算などが望ましいが、今回は全履歴計算とする
        
        $sql = "SELECT 
                    SUM(CASE WHEN `区分` = 1 THEN `数量` ELSE 0 END) as total_in,
                    SUM(CASE WHEN `区分` = 2 THEN `数量` ELSE 0 END) as total_out
                FROM `T_入出庫履歴` 
                WHERE `商品ID` = :productId AND `削除フラグ` = 0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        $stock = ($result['total_in'] ?? 0) - ($result['total_out'] ?? 0);
        return (int)$stock;
    }

    /**
     * 棚卸データを登録する
     * 
     * @param int $productId
     * @param int $warehouseId
     * @param int $shelfId
     * @param int $actualStock 実在庫
     */
    public function registerInventory($productId, $warehouseId, $shelfId, $actualStock, $userId = 1) {
        // 理論在庫の再取得
        $theoreticalStock = $this->getTheoreticalStock($productId);
        $diff = $actualStock - $theoreticalStock;
        
        // T_棚卸履歴 への登録
        $sql = "INSERT INTO `T_棚卸履歴` (
                    `年月`, `棚卸日`, `倉庫ID`, `棚ID`, `商品ID`, 
                    `理論在庫`, `実在庫`, `差異`, `確定フラグ`, `確定日時`, 
                    `登録者`, `登録日時`, `更新者`, `更新日時`
                ) VALUES (
                    :ym, :date, :warehouse_id, :shelf_id, :product_id,
                    :theoretical, :actual, :diff, 1, NOW(),
                    :user_id, NOW(), :user_id, NOW()
                )";
        
        $ym = (int)date('Ym');
        $date = date('Y-m-d');
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':ym', $ym, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':warehouse_id', $warehouseId, PDO::PARAM_INT);
        $stmt->bindValue(':shelf_id', $shelfId, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':theoretical', $theoreticalStock, PDO::PARAM_INT);
        $stmt->bindValue(':actual', $actualStock, PDO::PARAM_INT);
        $stmt->bindValue(':diff', $diff, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $this->pdo->lastInsertId();
    }
}
