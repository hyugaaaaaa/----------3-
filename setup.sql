-- 1. 汎用コードマスタ（複合キーのためAUTO_INCREMENTは不要）
CREATE TABLE `M_汎用コードマスタ` (
    `カテゴリID` INT NOT NULL,
    `カテゴリ名` VARCHAR(30) NOT NULL,
    `区分コード` INT NOT NULL,
    `区分名` VARCHAR(30) NOT NULL,
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`カテゴリID`, `区分コード`)
);

-- 2. 倉庫マスタ
CREATE TABLE `M_倉庫マスタ` (
    `倉庫ID` INT AUTO_INCREMENT NOT NULL, -- 自動採番を追加
    `倉庫コード` VARCHAR(20) NOT NULL,
    `倉庫名` VARCHAR(10),
    `有効フラグ` TINYINT(1),
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`倉庫ID`)
);

-- 3. 棚マスタ
CREATE TABLE `M_棚マスタ` (
    `棚ID` INT AUTO_INCREMENT NOT NULL, -- 自動採番を追加
    `倉庫ID` INT NOT NULL,
    `棚コード` VARCHAR(20) NOT NULL,
    `棚名称` VARCHAR(10),
    `有効フラグ` TINYINT(1),
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`棚ID`)
);

-- 4. 商品マスタ
CREATE TABLE `M_商品マスタ` (
    `商品ID` INT AUTO_INCREMENT NOT NULL, -- ここがエラーの原因（自動採番を追加）
    `商品コード` VARCHAR(20) NOT NULL,
    `商品名` VARCHAR(256) NOT NULL,
    `カテゴリ` INT NOT NULL,
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`商品ID`)
);

-- 5. ユーザーマスタ
CREATE TABLE `M_ユーザーマスタ` (
    `ユーザID` INT AUTO_INCREMENT NOT NULL, -- 自動採番を追加
    `ユーザ名` VARCHAR(10) NOT NULL,
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`ユーザID`)
);

-- 6. 月次在庫（複合キーのためAUTO_INCREMENTは不要）
CREATE TABLE `T_月次在庫` (
    `年月` INT NOT NULL,
    `倉庫ID` INT NOT NULL,
    `棚ID` INT NOT NULL,
    `商品ID` INT NOT NULL,
    `繰越在庫数` INT NOT NULL,
    `当月入庫数` INT NOT NULL,
    `当月出庫数` INT NOT NULL,
    `当月在庫数` INT NOT NULL,
    `確定フラグ` TINYINT(1),
    `確定日` DATE,
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`年月`, `倉庫ID`, `棚ID`, `商品ID`)
);

-- 7. 入出庫履歴
CREATE TABLE `T_入出庫履歴` (
    `履歴ID` INT AUTO_INCREMENT NOT NULL, -- 自動採番を追加
    `対象年月` INT NOT NULL,
    `倉庫ID` INT NOT NULL,
    `棚ID` INT NOT NULL,
    `商品ID` INT NOT NULL,
    `区分` INT NOT NULL,
    `数量` INT NOT NULL,
    `備考` VARCHAR(100),
    `削除フラグ` TINYINT(1),
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`履歴ID`)
);

-- 8. 棚卸履歴
CREATE TABLE `T_棚卸履歴` (
    `棚卸ID` INT AUTO_INCREMENT NOT NULL, -- 自動採番を追加
    `年月` INT NOT NULL,
    `棚卸日` DATE NOT NULL,
    `倉庫ID` INT NOT NULL,
    `棚ID` INT NOT NULL,
    `商品ID` INT NOT NULL,
    `理論在庫` INT NOT NULL,
    `実在庫` INT NOT NULL,
    `差異` INT NOT NULL,
    `確定フラグ` TINYINT(1),
    `確定日時` DATETIME,
    `登録者` INT NOT NULL,
    `登録日時` DATETIME NOT NULL,
    `更新者` INT NOT NULL,
    `更新日時` DATETIME NOT NULL,
    PRIMARY KEY (`棚卸ID`)
);