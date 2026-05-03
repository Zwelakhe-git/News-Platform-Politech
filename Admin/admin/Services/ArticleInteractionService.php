<?php
namespace Thunderpc\Vkurse\Admin\Services;

require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;

use Thunderpc\Vkurse\Admin\Utils\Log;

Log::init();
class ArticleInteractionService extends Database
{   
    // Пороги для сброса в БД
    const LIKES_BATCH_THRESHOLD = 1000;
    const COMMENTS_BATCH_THRESHOLD = 1000;
    
    // Ключи Redis
    const LIKES_QUEUE_KEY = 'likes_queue';
    const COMMENTS_QUEUE_KEY = 'comments_queue';
    const LIKES_COUNT_KEY_PREFIX = 'likes_count:';
    const COMMENTS_COUNT_KEY_PREFIX = 'comments_count:';
    const LIKES_PENDING_KEY = 'likes_pending_flush';
    const COMMENTS_PENDING_KEY = 'comments_pending_flush';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Поставить лайк статье
     * Сохраняет в Redis, сбрасывает в БД при достижении порога
     */
    public function likeArticle($articleId, $userId)
    {
        try {
            // Проверка: не лайкнул ли пользователь уже эту статью (опционально)
            if ($this->hasUserLikedArticle($articleId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'User already liked this article'
                ];
            }
            
            // Сохраняем лайк в Redis (для быстрой записи)
            $item = [
                'user_id' => $userId,
                'article_id' => $articleId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $this->redisClient->lPush(self::LIKES_QUEUE_KEY, json_encode($item, JSON_UNESCAPED_UNICODE));
            
            // Увеличиваем счетчик лайков для статьи
            $likesCount = $this->redisClient->incr(self::LIKES_COUNT_KEY_PREFIX . $articleId);
            
            // Отмечаем, что есть данные для сброса в БД
            $this->redisClient->sAdd(self::LIKES_PENDING_KEY, $articleId);
            
            // Если достигнут порог, сбрасываем все накопленные лайки для этой статьи в БД
            if ($likesCount >= self::LIKES_BATCH_THRESHOLD) {
                $this->flushLikesToDatabase($articleId);
            }
            
            // Обновляем общее количество лайков в статье (для быстрого отображения)
            $this->updateArticleLikesCount($articleId);
            
            return [
                'success' => true,
                'message' => 'Like stored successfully',
                'likes_count' => $likesCount
            ];
            
        } catch (\RedisException $e) {
            Log::error("Redis error in likeArticle: {$e->getMessage()}");
            // Fallback: пробуем сохранить напрямую в БД
            return $this->likeArticleDirect($articleId, $userId);
        } catch (\Exception $e) {
            Log::error("Error in likeArticle: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Server error'
            ];
        }
    }
    
    /**
     * Добавить комментарий к статье
     */
    public function commentOnArticle($articleId, $userId, $content)
    {
        try {
            // Валидация контента
            if (empty(trim($content))) {
                return [
                    'success' => false,
                    'message' => 'Comment content cannot be empty'
                ];
            }
            
            // Сохраняем комментарий в Redis
            $item = [
                'user_id' => $userId,
                'article_id' => $articleId,
                'content' => htmlspecialchars($content, ENT_QUOTES, 'UTF-8'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $this->redisClient->lPush(self::COMMENTS_QUEUE_KEY, json_encode($item, JSON_UNESCAPED_UNICODE));
            
            // Увеличиваем счетчик комментариев для статьи
            $commentsCount = $this->redisClient->incr(self::COMMENTS_COUNT_KEY_PREFIX . $articleId);
            
            // Отмечаем, что есть данные для сброса в БД
            $this->redisClient->sAdd(self::COMMENTS_PENDING_KEY, $articleId);
            
            // Если достигнут порог, сбрасываем все накопленные комментарии для этой статьи в БД
            if ($commentsCount >= self::COMMENTS_BATCH_THRESHOLD) {
                $this->flushCommentsToDatabase($articleId);
            }
            
            // Обновляем общее количество комментариев в статье
            $this->updateArticleCommentsCount($articleId);
            
            return [
                'success' => true,
                'message' => 'Comment stored successfully',
                'comments_count' => $commentsCount
            ];
            
        } catch (\RedisException $e) {
            Log::error("Redis error in commentOnArticle: {$e->getMessage()}");
            // Fallback: пробуем сохранить напрямую в БД
            return $this->commentOnArticleDirect($articleId, $userId, $content);
        } catch (\Exception $e) {
            Log::error("Error in commentOnArticle: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Server error'
            ];
        }
    }
    
    /**
     * Сбросить накопленные лайки для статьи в БД
     */
    public function flushLikesToDatabase($articleId = null)
    {
        try {
            if ($articleId) {
                // Сбрасываем только для конкретной статьи
                $pendingArticles = [$articleId];
            } else {
                // Сбрасываем для всех статей, у которых есть накопления
                $pendingArticles = $this->redisClient->sMembers(self::LIKES_PENDING_KEY);
            }
            
            foreach ($pendingArticles as $aid) {
                $likes = [];
                $queueKey = self::LIKES_QUEUE_KEY;
                
                // Извлекаем все лайки для этой статьи из очереди
                $queueLength = $this->redisClient->lLen($queueKey);
                for ($i = 0; $i < $queueLength; $i++) {
                    $item = json_decode($this->redisClient->lIndex($queueKey, $i), true);
                    if ($item && $item['article_id'] == $aid) {
                        $likes[] = $item;
                    }
                }
                
                if (empty($likes)) {
                    $this->redisClient->sRem(self::LIKES_PENDING_KEY, $aid);
                    continue;
                }
                
                // Удаляем эти лайки из очереди
                foreach ($likes as $like) {
                    $this->redisClient->lRem($queueKey, 1, json_encode($like, JSON_UNESCAPED_UNICODE));
                }
                
                // Сохраняем в БД пакетно
                $this->batchInsertLikes($likes);
                
                // Сбрасываем счетчик в Redis
                $this->redisClient->del(self::LIKES_COUNT_KEY_PREFIX . $aid);
                $this->redisClient->sRem(self::LIKES_PENDING_KEY, $aid);
            }
            
            return ['success' => true, 'flushed' => count($pendingArticles)];
            
        } catch (\Exception $e) {
            Log::error("Error flushing likes to database: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Сбросить накопленные комментарии для статьи в БД
     */
    public function flushCommentsToDatabase($articleId = null)
    {
        try {
            if ($articleId) {
                $pendingArticles = [$articleId];
            } else {
                $pendingArticles = $this->redisClient->sMembers(self::COMMENTS_PENDING_KEY);
            }
            
            foreach ($pendingArticles as $aid) {
                $comments = [];
                $queueKey = self::COMMENTS_QUEUE_KEY;
                
                $queueLength = $this->redisClient->lLen($queueKey);
                for ($i = 0; $i < $queueLength; $i++) {
                    $item = json_decode($this->redisClient->lIndex($queueKey, $i), true);
                    if ($item && $item['article_id'] == $aid) {
                        $comments[] = $item;
                    }
                }
                
                if (empty($comments)) {
                    $this->redisClient->sRem(self::COMMENTS_PENDING_KEY, $aid);
                    continue;
                }
                
                foreach ($comments as $comment) {
                    $this->redisClient->lRem($queueKey, 1, json_encode($comment, JSON_UNESCAPED_UNICODE));
                }
                
                $this->batchInsertComments($comments);
                
                $this->redisClient->del(self::COMMENTS_COUNT_KEY_PREFIX . $aid);
                $this->redisClient->sRem(self::COMMENTS_PENDING_KEY, $aid);
            }
            
            return ['success' => true, 'flushed' => count($pendingArticles)];
            
        } catch (\Exception $e) {
            Log::error("Error flushing comments to database: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Пакетная вставка лайков в БД
     */
    private function batchInsertLikes(array $likes)
    {
        if (empty($likes)) {
            return;
        }
        
        $values = [];
        $params = [];
        $i = 0;
        
        foreach ($likes as $like) {
            $values[] = "(?, ?, ?)";
            $params[] = $like['user_id'];
            $params[] = $like['article_id'];
            $params[] = $like['created_at'];
            $i++;
        }
        
        $sql = "INSERT IGNORE INTO user_likes (user_id, article_id, created_at) VALUES " . implode(", ", $values);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    /**
     * Пакетная вставка комментариев в БД
     */
    private function batchInsertComments(array $comments)
    {
        if (empty($comments)) {
            return;
        }
        
        $values = [];
        $params = [];
        
        foreach ($comments as $comment) {
            $values[] = "(?, ?, ?, ?)";
            $params[] = $comment['user_id'];
            $params[] = $comment['article_id'];
            $params[] = $comment['content'];
            $params[] = $comment['created_at'];
        }
        
        $sql = "INSERT INTO comments (user_id, article_id, content, created_at) VALUES " . implode(", ", $values);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    /**
     * Обновить общее количество лайков в таблице статей
     */
    private function updateArticleLikesCount($articleId)
    {
        $count = $this->redisClient->get(self::LIKES_COUNT_KEY_PREFIX . $articleId);
        if ($count) {
            $stmt = $this->pdo->prepare("UPDATE articles SET likes_count = likes_count + ? WHERE id = ?");
            $stmt->execute([$count, $articleId]);
        }
    }
    
    /**
     * Обновить общее количество комментариев в таблице статей
     */
    private function updateArticleCommentsCount($articleId)
    {
        $count = $this->redisClient->get(self::COMMENTS_COUNT_KEY_PREFIX . $articleId);
        if ($count) {
            $stmt = $this->pdo->prepare("UPDATE articles SET comments_count = comments_count + ? WHERE id = ?");
            $stmt->execute([$count, $articleId]);
        }
    }
    
    /**
     * Проверить, лайкнул ли пользователь статью
     */
    private function hasUserLikedArticle($articleId, $userId)
    {
        // Сначала проверяем в Redis (для быстрых повторных лайков)
        $queueKey = self::LIKES_QUEUE_KEY;
        $queueLength = $this->redisClient->lLen($queueKey);
        
        for ($i = 0; $i < $queueLength; $i++) {
            $item = json_decode($this->redisClient->lIndex($queueKey, $i), true);
            if ($item && $item['article_id'] == $articleId && $item['user_id'] == $userId) {
                return true;
            }
        }
        
        // Затем проверяем в БД
        $stmt = $this->pdo->prepare("SELECT 1 FROM user_likes WHERE article_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$articleId, $userId]);
        return $stmt->fetchColumn() !== false;
    }
    
    /**
     * Fallback: прямой лайк в БД (если Redis недоступен)
     */
    private function likeArticleDirect($articleId, $userId)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO user_likes (user_id, article_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$userId, $articleId]);
            $stmt2 = $this->pdo->prepare("UPDATE articles SET likes_count = likes_count + 1 WHERE id = ?");
            $stmt2->execute([$articleId]);
            return [
                'success' => true,
                'message' => 'Like stored directly in database'
            ];
        } catch (\PDOException $e) {
            Log::error("Direct like insert failed: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Server error'
            ];
        }
    }
    
    /**
     * Fallback: прямой комментарий в БД (если Redis недоступен)
     */
    private function commentOnArticleDirect($articleId, $userId, $content)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO comments (user_id, article_id, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$userId, $articleId, $content]);
            $stmt2 = $this->pdo->prepare("UPDATE articles SET comments_count = comments_count + 1 WHERE id = ?");
            $stmt2->execute([$articleId]);
            return [
                'success' => true,
                'message' => 'Comment stored directly in database'
            ];
        } catch (\PDOException $e) {
            Log::error("Direct comment insert failed: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Server error'
            ];
        }
    }
    
    /**
     * Cron-задача: периодический сброс всех накопленных данных в БД
     * Запускать каждые 5-10 минут
     */
    public function flushAllPendingData()
    {
        $this->flushLikesToDatabase();
        $this->flushCommentsToDatabase();
        
        return [
            'success' => true,
            'message' => 'All pending data flushed to database'
        ];
    }
    
    /**
     * Получить актуальное количество лайков для статьи (Redis + БД)
     */
    public function getArticleLikesCount($articleId)
    {
        $redisCount = $this->redisClient->get(self::LIKES_COUNT_KEY_PREFIX . $articleId) ?: 0;
        
        $stmt = $this->pdo->prepare("SELECT likes_count FROM articles WHERE id = ?");
        $stmt->execute([$articleId]);
        $dbCount = $stmt->fetchColumn() ?: 0;
        
        return $dbCount + $redisCount;
    }
    
    /**
     * Получить актуальное количество комментариев для статьи
     */
    public function getArticleCommentsCount($articleId)
    {
        $redisCount = $this->redisClient->get(self::COMMENTS_COUNT_KEY_PREFIX . $articleId) ?: 0;
        
        $stmt = $this->pdo->prepare("SELECT comments_count FROM articles WHERE id = ?");
        $stmt->execute([$articleId]);
        $dbCount = $stmt->fetchColumn() ?: 0;
        
        return $dbCount + $redisCount;
    }
}
?>