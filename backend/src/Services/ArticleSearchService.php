<?php
namespace Thunderpc\Vkurse\Services;

require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';
use Thunderpc\Vkurse\Models\Database;
use Thunderpc\Vkurse\Models\Elasticsearch;
use Thunderpc\Vkurse\Auth\Auth;
use Thunderpc\Vkurse\Utils\Log;

Log::init();

class ArticleSearchService extends Database
{
    private $elasticSearch;    
    // Конфигурация кэширования
    const CACHE_TTL = 86400; // 1 день (24 часа * 3600 секунд)
    const CACHE_KEY_PREFIX = 'search:';
    
    public function __construct()
    {
        parent::__construct();
        $this->elasticSearch = new Elasticsearch();
    }
    
    /**
     * Поиск статей с кэшированием результатов в Redis
     */
    public function searchArticle($req, $res)
    {
        try {
            
            $query = trim((string)($_GET['q'] ?? ''));
            $userId = $_SESSION['user']['id'];//$this->getUserIdFromRequest($req); // Получаем ID пользователя из токена/сессии
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 20);
            
            // Валидация запроса
            if (empty($query)) {
                return $res->json([
                    'success' => false,
                    'message' => 'Search query cannot be empty'
                ]);
            }
            
            if (strlen($query) < 2) {
                return $res->json([
                    'success' => false,
                    'message' => 'Search query must be at least 2 characters'
                ]);
            }
            
            // Генерируем уникальный ключ кэша
            $cacheKey = $this->generateCacheKey($query, $page, $limit);
            
            // Пытаемся получить результаты из кэша Redis
            $cachedResult = $this->getFromCache($cacheKey);
            Log::info("searching for $query");
            if ($cachedResult !== null) {
                // Результаты найдены в кэше
                Log::info("Search results served from cache for query: {$query}");
                
                // Асинхронно записываем историю поиска (не блокируем ответ)
                $this->recordSearchHistoryAsync($userId, $query, count($cachedResult['articles'] ?? []));
                
                return $res->json([
                    'success' => true,
                    'date' => date('Y-m-d H:i:s'),
                    'cached' => true,
                    'data' => [
                        'query' => $query,
                        'count' => $cachedResult['total'] ?? 0,
                        'page' => $page,
                        'limit' => $limit,
                        'articles' => $cachedResult['articles'] ?? []
                    ]
                ]);
            }
            
            // Кэш не найден — выполняем поиск в Elasticsearch
            Log::info("Cache miss for query: {$query}, performing Elasticsearch query");
            
            $response = $this->elasticSearch->esClient->search([
                'index' => 'news',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title^3', 'content'],
                        ],
                    ],
                    'from' => ($page - 1) * $limit,
                    'size' => $limit,
            ]]);
            
            $total = $response['hits']['total']['value'];
            $results = [];
            foreach (($response['hits']['hits'] ?? []) as $hit) {
                $results[] = array_merge(['id' => $hit['_id']], $hit['_source'] ?? []);
            }
            
            if ($total === 0) {
                // Кэшируем пустой результат (чтобы не ходить в ES каждый раз)
                $this->saveToCache($cacheKey, [
                    'total' => 0,
                    'articles' => []
                ], 3600); // Пустые результаты кэшируем на 1 час
                
                return $res->json([
                    'success' => false,
                    'message' => "No results found for query: {$query}",
                    'query' => $query
                ]);
            }
            
            // Сохраняем результаты в кэш Redis на 1 день
            $this->saveToCache($cacheKey, [
                'total' => $total,
                'articles' => $results
            ], self::CACHE_TTL);
            
            // Записываем историю поиска в БД (асинхронно или синхронно)
            $this->recordSearchHistory($userId, $query, $total);
            
            // Записываем просмотренные статьи в user_reading_history
            if ($userId && !empty($results)) {
                $this->recordReadingHistory($userId, $results);
            }
            
            return $res->json([
                'success' => true,
                'date' => date('Y-m-d H:i:s'),
                'cached' => false,
                'data' => [
                    'query' => $query,
                    'count' => $total ?? 0,
                    'page' => $page,
                    'limit' => $limit,
                    'articles' => $results
                ]
            ]);
            
        } catch (\Elasticsearch\Common\Exceptions\NoNodesAvailableException $e) {
            Log::error("Elasticsearch connection error: {$e->getMessage()}");
            return $res->status(503)->json([
                'success' => false,
                'message' => 'Search service temporarily unavailable'
            ]);
        } catch (\RedisException $e) {
            Log::error("Redis error in searchArticle: {$e->getMessage()}");
            // Fallback: продолжаем без кэша, но логируем ошибку
            return $this->searchWithoutCache($query, $page, $limit, $userId, $res);
        } catch (\PDOException $e) {
            Log::error("Database error in searchArticle: {$e->getMessage()}");
            // Не возвращаем ошибку пользователю, просто логируем
            return $res->json([
                'success' => true,
                'date' => date('Y-m-d H:i:s'),
                'warning' => 'Search history not recorded',
                'data' => [
                    'query' => $query,
                    'count' => $result['total'] ?? 0,
                    'articles' => $result['data'] ?? []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Unexpected error in searchArticle: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error during search'
            ]);
        } catch (\Throwable $e) {
            Log::error("Unexpected error in searchArticle: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error during search'
            ]);
        }
    }
    
    /**
     * Поиск без кэша (fallback при недоступности Redis)
     */
    private function searchWithoutCache($query, $page, $limit, $userId, $res)
    {
        try {
            $result = $this->elasticSearch->esClient->search($query, 'news', ['title^3', 'content'], $page, $limit);
            
            if ($userId && !empty($result['data'])) {
                $this->recordReadingHistory($userId, $result['data']);
            }
            
            return $res->json([
                'success' => true,
                'date' => date('Y-m-d H:i:s'),
                'cached' => false,
                'cache_disabled' => true,
                'data' => [
                    'query' => $query,
                    'count' => $result['total'] ?? 0,
                    'articles' => $result['data'] ?? []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Fallback search failed: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Search service unavailable'
            ]);
        }
    }
    
    /**
     * Генерация уникального ключа кэша для поискового запроса
     */
    private function generateCacheKey($query, $page, $limit)
    {
        $normalizedQuery = mb_strtolower(trim($query));
        $normalizedQuery = preg_replace('/\s+/', ' ', $normalizedQuery);
        
        return self::CACHE_KEY_PREFIX . md5($normalizedQuery . '_' . $page . '_' . $limit);
    }
    
    /**
     * Получение результатов из кэша Redis
     */
    private function getFromCache($key)
    {
        try {
            $cached = $this->redisClient->get($key);
            if ($cached) {
                return json_decode($cached, true);
            }
        } catch (\RedisException $e) {
            Log::warning("Failed to read from Redis cache: {$e->getMessage()}");
        }
        
        return null;
    }
    
    /**
     * Сохранение результатов в кэш Redis
     */
    private function saveToCache($key, $data, $ttl)
    {
        try {
            $this->redisClient->setex($key, $ttl, json_encode($data, JSON_UNESCAPED_UNICODE));
            Log::debug("Cached search results with key: {$key}, TTL: {$ttl}s");
        } catch (\RedisException $e) {
            Log::warning("Failed to save to Redis cache: {$e->getMessage()}");
        }
    }
    
    /**
     * Запись истории поиска в БД
     */
    private function recordSearchHistory($userId, $query, $resultsCount)
    {
        if (!$userId) {
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO user_search_history (user_id, search_query, results_count, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $query, $resultsCount]);
        } catch (\PDOException $e) {
            Log::error("Failed to record search history: {$e->getMessage()}");
        }
    }
    
    /**
     * Асинхронная запись истории поиска (через очередь или отдельный поток)
     */
    private function recordSearchHistoryAsync($userId, $query, $resultsCount)
    {
        if (!$userId) {
            return;
        }
        
        // Опционально: можно отправить в очередь Redis
        try {
            $item = [
                'user_id' => $userId,
                'search_query' => $query,
                'results_count' => $resultsCount,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->redisClient->lPush('search_history_queue', json_encode($item, JSON_UNESCAPED_UNICODE));
        } catch (\RedisException $e) {
            // Если очередь недоступна, пишем синхронно
            $this->recordSearchHistory($userId, $query, $resultsCount);
        }
    }
    
    /**
     * Запись просмотренных статей в user_reading_history
     */
    private function recordReadingHistory($userId, array $articles)
    {
        if (empty($articles)) {
            return;
        }
        
        try {
            $values = [];
            $params = [];
            
            foreach ($articles as $article) {
                // Определяем тип статьи (внутренняя или внешняя)
                $articleId = $article['id'] ?? null;
                $externalArticleId = $article['external_id'] ?? null;
                
                if ($articleId) {
                    $values[] = "(?, ?, NULL, ?, NOW())";
                    $params[] = $userId;
                    $params[] = $articleId;
                    $params[] = 'search';
                } elseif ($externalArticleId) {
                    $values[] = "(?, NULL, ?, ?, NOW())";
                    $params[] = $userId;
                    $params[] = $externalArticleId;
                    $params[] = 'search';
                } else {
                    continue;
                }
            }
            
            if (empty($values)) {
                return;
            }
            
            // Используем INSERT IGNORE чтобы избежать дубликатов
            $sql = "INSERT IGNORE INTO user_reading_history 
                    (user_id, article_id, external_article_id, source, read_at) 
                    VALUES " . implode(", ", $values);
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            Log::debug("Recorded reading history for user {$userId}, " . count($articles) . " articles");
            
        } catch (\PDOException $e) {
            Log::error("Failed to record reading history: {$e->getMessage()}");
        }
    }
    
    /**
     * Получение ID пользователя из запроса (JWT или сессия)
     */
    private function getUserIdFromRequest($req)
    {
        // Пример: извлекаем из заголовка Authorization
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                // Декодируем JWT и получаем user_id
                $payload = $this->decodeJWT($token);
                return $payload['user_id'] ?? null;
            } catch (\Exception $e) {
                Log::warning("Failed to decode JWT: {$e->getMessage()}");
            }
        }
        
        // Если пользователь не авторизован — возвращаем null
        return null;
    }
    
    /**
     * Декодирование JWT (упрощённая версия)
     */
    private function decodeJWT($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid JWT format');
        }
        
        $payload = json_decode(base64_decode($parts[1]), true);
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            throw new \Exception('JWT expired');
        }
        
        return $payload;
    }
    
    /**
     * Инвалидация кэша для конкретного запроса
     */
    public function invalidateCache($query)
    {
        $normalizedQuery = mb_strtolower(trim($query));
        $normalizedQuery = preg_replace('/\s+/', ' ', $normalizedQuery);
        
        // Удаляем все возможные пагинированные версии этого запроса
        $pattern = self::CACHE_KEY_PREFIX . md5($normalizedQuery . '_*');
        
        try {
            $keys = $this->redisClient->keys($pattern);
            if (!empty($keys)) {
                $this->redisClient->del($keys);
                Log::info("Invalidated cache for query: {$query}, " . count($keys) . " keys removed");
            }
        } catch (\RedisException $e) {
            Log::error("Failed to invalidate cache: {$e->getMessage()}");
        }
    }
    
    /**
     * Массовая инвалидация старых кэшей (cron-задача)
     */
    public function cleanExpiredCache()
    {
        // Redis сам удаляет ключи по TTL, но можно добавить дополнительную логику
        Log::info("Cache cleanup completed: Redis handles TTL automatically");
    }
}