<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Only POST allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$user_id = (int)($data['user_id'] ?? 0);
$post_id = (int)($data['post_id'] ?? 0);
$like = (bool)($data['like'] ?? false);

if ($user_id <= 0 || $post_id <= 0) {
    jsonResponse(['error' => 'user_id and post_id are required'], 400);
}

if ($like) {
    $item = [
        'user_id' => $user_id,
        'post_id' => $post_id,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    getRedis()->lPush('likes_queue', json_encode($item, JSON_UNESCAPED_UNICODE));
}

jsonResponse(['ok' => true, 'msg' => 'Like stored in Redis']);