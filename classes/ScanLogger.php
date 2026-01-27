<?php

class ScanLogger {
    private $logFile = __DIR__ . '/../scan_debug.log';

    public function log($code, $result) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] Code: $code, Result: $result" . PHP_EOL;

        // デバッグ用にファイルへ出力 (本番ではDBへのINSERTになる)
        // ログファイルは posic/scan_debug.log に作成されます
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
