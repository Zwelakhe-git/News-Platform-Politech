<?php
require_once __DIR__ . '/../config.php';

$post_id = (int)($_GET['post_id'] ?? 0);

if ($post_id <= 0) {
    jsonResponse(['error' => 'post_id required'], 400);
}

$item = [
    'post_id' => $post_id,
    'created_at' => date('Y-m-d H:i:s'),
];

getRedis()->lPush('views_queue', json_encode($item, JSON_UNESCAPED_UNICODE));

jsonResponse(['ok' => true, 'msg' => 'View stored in Redis']);