<?php
namespace Thunderpc\Vkurse\Models;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use function curl_init;
use function curl_setopt;
use function curl_exec;
use function curl_close;

use Thunderpc\Vkurse\Models\Database;
use Thunderpc\Vkurse\Models\Elasticsearch;
use Thunderpc\Vkurse\Models\CategoryModel;
use Thunderpc\Vkurse\Utils\SlugGenerator;
use Thunderpc\Vkurse\Utils\Log;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');

$dotenv->load();
Log::init();
class ArticleModel extends Database {
    private $categoryModel;
    private $elasticSearch;

    public function __construct(){
        parent::__construct();
        $this->categoryModel = new CategoryModel();
        $this->elasticSearch = new Elasticsearch();
    }
    
    
    public function getArticleById($id) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, SELECT a.*, c.name, u.full_name as author_name, u.avatar_url, u.email
            FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id
            LEFT JOIN users u ON u.id = a.author_id
            WHERE a.status = 'published'
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getExternalArticlesBySource($sourceId){}
    
    public function getAllArticles($source='external') {
        Log::info('getAllArticles: source ' . $source);
        switch($source){
            case 'local':
                return $this->getLocalArticles();
            case 'external':
                return $this->getExternalArticles();
            case 'all':
                return ['local' => $this->getLocalArticles(), 'external' => $this->getExternalArticles()];
            default:
                return $this->getLocalArticles();
        }
    }
    public function getLocalArticles() {
        /**
         * все новости, идушие на главную страницу
         */
        Log::info('attempting to fetch local articles');
        try{
            $columns = "a.id,a.title,a.slug,a.content,a.cover_image_url AS image_url,a.status,a.published_at,a.created_at,a.is_breaking";
            $columns .= ",a.views_count,a.likes_count,a.comments_count";
            $stmt = $this->pdo->query("
                SELECT $columns, c.name AS category, u.full_name as author_name, u.avatar_url, u.email
                FROM articles a
                LEFT JOIN categories c ON c.id = a.category_id
                LEFT JOIN users u ON u.id = a.author_id
                WHERE a.status = 'published' AND c.is_active = true
                ORDER BY a.published_at DESC
            ");
            Log::info('query executed without errors');
            return $stmt->fetchAll();
        } catch(PDOException $e){
            Log::error("ArticleModel - {$e->getMessage()}");
            return [];
        } catch(Exception $e){
            Log::error("ArticleModel - {$e->getMessage()}");
            return [];
        }
    }

    public function getArticlesByAuthor($authorId){
        try {
            $columns = "a.id,a.title,a.slug,a.content,a.cover_image_url AS image_url,a.status,a.published_at,a.created_at,a.is_breaking";
            $columns .= ",a.views_count,a.likes_count,a.comments_count";
            $sql = "
                SELECT $columns, c.name AS category, u.full_name as author_name, u.avatar_url, u.email
                FROM articles a
                LEFT JOIN categories c ON c.id = a.category_id
                LEFT JOIN users u ON u.id = a.author_id
                WHERE a.author_id = ?
                ORDER BY a.published_at DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$authorId]);
            
            return $stmt->fetchAll();
        } catch(\PDOException $e){
            Log::error($e->getMessage());
        }
    }

    public function getExternalArticles(){
        try{
            $fields = ['ea.id', 'ea.title', 'ea.published_at', 'ea.author_name', 'ea.url', 'ea.image_url', 'ea.content'];
            Log::info("ArticleModel - getExternalArticles - Attempting to fetch external articles");
            //refreshExternalArticles();
            $stmt = $this->pdo->query("SELECT " . implode(',', $fields) . ", c.name AS category, es.name AS source FROM external_articles ea
            LEFT JOIN external_sources es ON es.id = ea.source_id LEFT JOIN categories c ON c.id = ea.category_id");
            $articles = $stmt->fetchAll();
            Log::info("Successfully fetched " . count($articles));
            return $articles;
        } catch(PDOException $e){
            Log::error("ArticleModel - getExternalArticles - {$e->getMessage()}");
            return [];
        }
    }
    
    public function getFromAPI($baseUrl, $apiKey, $category = 'all', $country = 'us', $pageSize = 10) {
        //$apiKey = $_ENV['NEWS_API_ORG_API_KEY'];
        $categories = [
            'business' => 10,
            'entertainment' => 10,
            'general' => 10,
            'health' => 10,
            'science' => 10,
            'sports' => 10,
            'technology' => 10,
            'politics' => 10
        ];

        // Инициализация cURL здесь, но URL будет меняться
        $ch = curl_init();
        
        // Общие настройки cURL вынесены из условий
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
        ]);

        if($category === 'all') {
            $articles = [];
            foreach($categories as $cat => $size) {
                //https://newsapi.org/v2/top-headlines
                $url = "$baseUrl?country=$country&category=$cat&apiKey=$apiKey&pageSize=$size";
                
                // Устанавливаем URL для каждого запроса
                curl_setopt($ch, CURLOPT_URL, $url);
                
                $response = curl_exec($ch);
                
                // Проверяем ошибки cURL
                if(curl_errno($ch)) {
                    Log::error('cURL Error for category ' . $cat . ': ' . curl_error($ch));
                    continue; // Пропускаем эту категорию при ошибке
                }
                
                $data = json_decode($response, true);
                
                if (isset($data['status']) && $data['status'] === 'ok' && isset($data['articles'])) {
                    foreach ($data['articles'] as $article) {
                        $article['category'] = $cat;
                        $article['from'] = 'external';
                        $articles[] = $article;
                    }
                } else {
                    Log::error('API Error for category ' . $cat . ': ' . json_encode($data));
                }
                
                // Пауза между запросами чтобы не превысить лимиты
                usleep(100000); // 0.1 секунда
            }
            
            curl_close($ch);
            return $articles;
            
        } elseif (array_key_exists($category, $categories)) {
            $url = "$baseUrl?country=$country&category=$category&apiKey=$apiKey&pageSize=$pageSize";
        } else {
            // Defau$baseUrlt recognized
            $url = "$baseUrl?country=$country&category=general&apiKey=$apiKey&pageSize=$pageSize";
        }
        
        // Устанавливаем URL для единичного запроса
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $response = curl_exec($ch);
        
        // Проверяем ошибки cURL
        if(curl_errno($ch)) {
            $error = 'cURL Error: ' . curl_error($ch);
            curl_close($ch);
            error_log($error);
            return ['status' => 'error', 'message' => $error];
        }
        
        curl_close($ch);
        $data = json_decode($response, true);
        
        if (isset($data['status']) && $data['status'] === 'ok' && isset($data['articles'])) {
            return $data['articles'];
        } else {
            error_log('API Error: ' . json_encode($data));
            return ['status' => 'error', 'message' => 'Failed to fetch news', 'api_response' => $data];
        }
    }

    /**
     * refreshes expired cached articles, deleting all that are expired.
     */
    public function refreshExternalArticles(){
        try{
            $stmt = $this->pdo->query("SELECT id FROM external_sources WHERE DATE_ADD(last_sync_at, INTERVAL update_interval_minutes 'MINUTE') < NOW() AND is_active = true");
            $sources = $stmt->fetchAll();
            if(empty($sources)) return;

            $this->pdo->beginTransaction();
            foreach($sources as $source){
                $this->pdo->query("DELETE FROM external_articles WHERE source_id = '{$source['id']}'");
                // the optimal way is to first delete then load
                $this->loadArticlesFromExternalSource($source['id']);
            }
            $this->pdo->commit();
        } catch(PDOException $e){
            if($this->pdo->inTransaction()){
                $this->pdo->rollBack();
            }
            Log::error("refreshExternalArticles - Failed to refresh articles: " . $e->getMessage());
        }
    }

    /**
     * loads articles from the external source. if cached articles are still active it ignores the fetch.
     * articles should either be deleted or expired for successful fetch
     * @param sourceId - id of the source, taken from external_sources. ensuring articles are from an existing source
     * @return bool
     */
    public function loadArticlesFromExternalSource($sourceId){
        try{
            $sql = "SELECT CASE WHEN DATE_ADD(last_sync_at, INTERVAL update_interval_minutes MINUTE) > NOW() THEN 'active' ELSE 'expired' END AS status FROM external_sources WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$sourceId]);

            if($stmt->fetchColumn() === 'active'){
                Log::warn("addExternalArticle failed to add. using cached articles instead");
                return [
                    'success' => false,
                    'message' => 'Using cached articles from the source'
                ];
            }
            $stmt = $this->pdo->prepare("SELECT id, name, base_url, api_key FROM external_sources WHERE id = ? AND is_active = true");
            $stmt->execute([$sourceId]);
            $source = $stmt->fetch();

            $count = 0;

            $articles = $this->getFromAPI($source['base_url'], $_ENV["{$source['api_key']}"]);
            foreach($articles as $article){
                $catId = $this->categoryModel->getCategoryId($article['category']);
                
                if($catId === -1){
                    $catId = $this->categoryModel->createCategory($article['category'])['category_id'];
                }
                
                $normalised = [
                    'source_id' => $sourceId,
                    'category_id' => $catId,
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'url' => $article['url'],
                    'image_url' => $article['urlToImage'],
                    'published_at' => $article['publishedAt'],
                    'author_name' => $article['author'],
                    'title_hash' => md5($article['title'])
                ];
                if(isset($article['source']['id'])){
                    $normalised['external_id'] = $article['source']['id'];
                }
                $stmt = $this->pdo->prepare("UPDATE external_sources SET last_sync_at = NOW() WHERE id = ?");
                $stmt->execute([$sourceId]);
                $result = $this->addExternalArticle($normalised);
                $count += $result > 0 ? 1 : 0;
            }

            return [
                'success' => true,
                'message' => "Successfully loaded {$count} articles from source {$source['name']}"
            ];
        } catch(PDOException $e){
            Log::error("ArticleModel - loadArticlesFromExternalSource - {$e->getMessage()}");
        }
        return false;
    }
    
    /**
     * adds local articles to the database
     * @return id of inserted article, -1 if there is an error
     */
    public function createArticle($data) {
        try{
            Log::info("creating article");
            $stmt = $this->pdo->prepare("
                INSERT INTO articles (author_id, category_id, title, slug, content, cover_image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $pdo = $this->pdo;
            $data['slug'] = SlugGenerator::makeUnique($data['title'], function($slug) use ($pdo) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
                $stmt->execute([$slug]);
                return $stmt->fetchColumn() > 0;
            });
            Log::info("article content: {$data['content']}");
            $res = $stmt->execute([
                $data['author_id'],
                $data['category_id'],
                $data['title'],
                $data['slug'],
                $data['content'],
                $data['cover_image_url'],
                $data['status']
            ]);

            $id = $this->pdo->lastInsertId();
            Log::info("article created: $id");
            if($this->elasticSearch->esClient){
                Log::info("attempting to save in ES");
                $this->elasticSearch->esClient->index([
                    'index' => 'news',
                    'id' => $id,
                    'body' => [
                        'title' => $data['title'],
                        'content' => $data['content'],
                        'author' => $_SESSION['user']['full_name'],
                        'cover_image_url' => $data['cover_image_url'],
                        'status' => $data['status'],
                        'category' => $data['category']
                    ]
                ]);
                Log::info("article saved in ES");
            } else {
                Log::warn("ES is not initialized");
            }
            
            return [
                'success' => $res,
                'id' => $res ? $this->pdo->lastInsertId() : -1,
                'message' => $res ? 'Article Successfully Created' : 'Server error'
            ];
        } catch(\PDOException $e){
            Log::error("ArticleModel - createArticle " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Server error'
            ];
        } catch(\Throwable $e){
            Log::error($e->getMessage());
            return [
                'success' => false,
                'message' => 'Server error'
            ];
        }
    }

    public function addExternalArticle($data){
        try{
            $params = ['source_id', 'category_id', 'title', 'content', 'url', 'image_url', 'published_at', 'author_name', 'title_hash'];
            $values = [$data['source_id'],$data['category_id'],$data['title'],$data['content'],$data['url'],$data['image_url'],$data['published_at'],$data['author_name'],$data['title_hash']];
            
            if(isset($data['external_id'])){
                $params[] = 'external_id';
                $values[] = $data['external_id'];
            }
            $placeHolders = str_repeat('?,', count($params) - 1) . '?';

            $sql = "INSERT INTO external_articles ('. implode(',', $params) .') VALUES ($placeHolders)";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($values);

            Log::info("ArticleModel - addExternalArticle - inserted new article");
            return $this->pdo->lastInsertId();
        } catch(PDOException $e){
            Log::error("addExternalArticle {$e->getMessage()}");
            return -1;
        }
    }
    
    // needs modification
    public function updateArticle($id, $data) {
        try{
            $this->pdo->beginTransaction();
            $params = "title=?,content=?,status=?,cover_image_url=?,category_id=?,updated_at=NOW()";
            $values = [
                $data['title'],
                $data['content'],
                $data['cover_image_url'],
                $data['category_id'],
                $data['status'],
            ];
            if($data['new_title']){
                $pdo = $this->pdo;
                $params .= ",slug=?";
                $values['slug'] = SlugGenerator::makeUnique($data['title'], function($slug) use ($pdo) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
                    $stmt->execute([$slug]);
                    return $stmt->fetchColumn() > 0;
                });
            }
            $values[] = $id;

            $stmt = $this->pdo->prepare("
                UPDATE articles 
                SET $params
                WHERE id = ?
            ");
            $result = $stmt->execute($values);
            $this->pdo->commit();
            return $result;
        } catch(PDOException $e){
            Log::error($e->getMessage());
            if($this->pdo->inTransaction()){
                $this->pdo->rollback();
            }
            return false;
        }
    }
    
    public function deleteArticle($id) {
        $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteExternalArticles($source_id){
        try{
            $stmt = $this->pdo->prepare('DELETE FROM external_articles WHERE source_id = ?');
            $stmt->execute([$source_id]);
            return $stmt->rowCount();
        } catch(PDOException $e){
            Log::error("deleteExternalArticles - {$e->getMessage()}");
        }
    }

    // ==================== MODERATION ====================

    /**
     * Получить статьи на модерацию
     */
    public function getArticlesForModeration($status = 'pending', $limit = 20, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.id, a.title, a.slug, a.content, a.status, a.created_at, a.published_at,
                       u.full_name as author_name, u.email as author_email, u.id as author_id,
                       c.name as category_name
                FROM articles a
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.status = ?
                ORDER BY a.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bindParam(1, $status, \PDO::PARAM_STR);
            $stmt->bindParam(2, $limit, \PDO::PARAM_INT);
            $stmt->bindParam(3, $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            Log::error("getArticlesForModeration error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Одобрить статью
     */
    public function approveArticle($articleId, $moderatorId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE articles 
                SET status = 'published', 
                    published_at = NOW(),
                    moderated_by = ?,
                    moderated_at = NOW()
                WHERE id = ?
            ");
            $res = $stmt->execute([$moderatorId, $articleId]);
            
            if ($res) {
                Log::info("Article $articleId approved by moderator $moderatorId");
                return ['success' => true, 'message' => 'Article approved successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to approve article'];
        } catch (\PDOException $e) {
            Log::error("approveArticle error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * Отклонить статью
     */
    public function rejectArticle($articleId, $moderatorId, $reason = '') {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE articles 
                SET status = 'rejected',
                    moderated_by = ?,
                    moderated_at = NOW(),
                    rejection_reason = ?
                WHERE id = ?
            ");
            $res = $stmt->execute([$moderatorId, $reason, $articleId]);
            
            if ($res) {
                Log::info("Article $articleId rejected by moderator $moderatorId. Reason: $reason");
                return ['success' => true, 'message' => 'Article rejected successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to reject article'];
        } catch (\PDOException $e) {
            Log::error("rejectArticle error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

}
?>