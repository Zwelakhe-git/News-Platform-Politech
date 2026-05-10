<?php
// config.php

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'news');
define('DB_USER', 'root');
define('DB_PASS', '');

define('REDIS_HOST', '100.108.77.80');
define('REDIS_PORT', 6379);

define('ELASTIC_HOST', 'http://127.0.0.1:9200');

function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function getRedis(): Redis
{
    static $redis = null;
    if ($redis === null) {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
    }
    return $redis;
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}