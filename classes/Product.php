<?php

class Product {
    // データベース接続のかわりにダミーデータを使用
    private $dummyData = [
        '4901330574369' => [
            'name' => 'ポテトチップス うすしお味',
            'price' => 150
        ],
        '4549131970258' => [
            'name' => 'ダイソー USBケーブル',
            'price' => 110
        ],
        'TEST12345' => [
            'name' => 'テスト商品A',
            'price' => 500
        ]
    ];

    /**
     * 商品コードから商品情報を取得する
     * 
     * @param string $code
     * @return array|null 商品情報配列、または見つからない場合はnull
     */
    public function getByCode($code) {
        if (isset($this->dummyData[$code])) {
            return $this->dummyData[$code];
        }
        return null;
    }
}
