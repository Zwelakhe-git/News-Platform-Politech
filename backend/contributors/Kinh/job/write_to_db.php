<?php
require_once __DIR__ . '/../config.php';

$pdo = getPDO();
$redis = getRedis();

function popBatch(Redis $redis, string $key, int $limit = 500): array
{
    $items = [];
    for ($i = 0; $i < $limit; $i++) {
        $raw = $redis->rPop($key);
        if ($raw === false) {
            break;
        }
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $items[] = $decoded;
        }
    }
    return $items;
}

function updateCounts(PDO $pdo, array $postIds, string $field, int $increment): void
{
    $postIds = array_values(array_unique(array_map('intval', $postIds)));
    if (!$postIds) {
        return;
    }

    $in = implode(',', array_fill(0, count($postIds), '?'));
    $sql = "UPDATE posts SET {$field} = {$field} + ? WHERE id IN ({$in})";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge([$increment], $postIds));
}

$likes = popBatch($redis, 'likes_queue', 500);
$views = popBatch($redis, 'views_queue', 500);
$comments = popBatch($redis, 'comments_queue', 500);

if ($likes) {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO user_likes (user_id, post_id, created_at) VALUES (?, ?, ?)');
        $postIds = [];
        foreach ($likes as $item) {
            $stmt->execute([$item['user_id'], $item['post_id'], $item['created_at']]);
            $postIds[] = $item['post_id'];
        }
        updateCounts($pdo, $postIds, 'likes_count', count($likes));
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Likes batch error: ' . $e->getMessage());
    }
}

if ($views) {
    $pdo->beginTransaction();
    try {
        $postIds = array_column($views, 'post_id');
        updateCounts($pdo, $postIds, 'views_count', count($views));
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Views batch error: ' . $e->getMessage());
    }
}

if ($comments) {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO comments (user_id, post_id, content, created_at) VALUES (?, ?, ?, ?)');
        $postIds = [];
        foreach ($comments as $item) {
            $stmt->execute([$item['user_id'], $item['post_id'], $item['content'], $item['created_at']]);
            $postIds[] = $item['post_id'];
        }
        updateCounts($pdo, $postIds, 'comments_count', count($comments));
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Comments batch error: ' . $e->getMessage());
    }
}

echo "Batch processing done\n";