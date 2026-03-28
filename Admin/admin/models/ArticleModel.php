<?php
namespace Thunderpc\Vkurse\Admin\Models;
require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;
use Thunderpc\Vkurse\Admin\Models\CategoryModel;
use Thunderpc\Vkurse\Admin\Utils\SlugGenerator;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');

$dotenv->load();

class ArticleModel extends Database {
    private $categoryModel;

    public function __construct(){
        parent::__construct();
        $this->categoryModel = new CategoryModel();
    }
    
    
    public function getArticleById($id) {
        $stmt = $this->pdo->prepare("
            SELECT a.*,
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
    public function getAllArticles($source='local') {
        switch($source){
            case 'local':
                return $this->getLocalArticles();
            case 'external':
                return $this->getExternalArticles();
            default:
                return $this->getLocalArticles();
        }
    }
    public function getLocalArticles() {
        /**
         * все новости, идушие на главную страницу
         */
        $res = $this->pdo->query("
            SELECT a.*,
            FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id
            LEFT JOIN users u ON u.id = a.author_id
            WHERE a.status = 'published'
            ORDER BY n.newsDate DESC
        ");
        return $res->fetchAll();
    }

    public function getExternalArticles(){
        try{            
            refreshExternalArticles();
            $res = $this->pdo->query("SELECT ea.* FROM external_articles ea
            LEFT JOIN external_sources es ON es.id = ea.source_id LEFT JOIN categories c ON c.id = ea.category_id");

            return $res->fetchAll();
        } catch(PDOException $e){}
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
                    error_log('cURL Error for category ' . $cat . ': ' . curl_error($ch));
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
                    error_log('API Error for category ' . $cat . ': ' . json_encode($data));
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
            $stmt = $this->pdo->query("SELECT id FROM external_sources WHERE TIMESTAMPADD(MINUTE,last_sync_at, update_interval_minutes) < NOW() AND is_active = true");
            $sources = $stmt->fetchAll();
            if(empty($sources)) return;

            $this->pdo->beginTransaction();
            foreach($sources as $source){
                $this->pdo->query("DELETE FROM external_articles WHERE source_id = {$source['id']}");
                $this->loadArticlesFromExternalSource($source['id']);
            }
            $this->pdo->commit();
        } catch(PDOException $e){
            if($this->pdo->inTransation()){
                $this->pdo->rollBack();
            }
            log_("refreshExternalArticles Failed to refresh articles: " . $e->getMessage());
        }
    }

    /**
     * loads articles from the external source. if cached articles are still active it ignores the fetch.
     * articles should either be deleted or expired for successful fetch
     * @param sourceId - id of the source, taken from external_sources. ensuring articles are from an existing source
     * @return array
     */
    public function loadArticlesFromExternalSource($sourceId){
        try{
            $sql = 'SELECT CASE WHEN TIMESTAMPADD(MINUTE,last_sync_at,update_interval_minutes) > NOW() THEN active ELSE expired END AS status FROM external_sources WHERE id = ?';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$data['source_id']]);

            if($stmt->fetchColumn() === 'active'){
                log_("addExternalArticle failed to add. using cached articles instead");
                return [
                    'success' => false,
                    'message' => 'Using cached articles from the source'
                ];
            }
            $stmt = $this->pdo->prepare("SELECT base_url, api_key FROM external_sources WHERE id = ? AND is_active = true");
            $stmt->execute([$sourceId]);
            $source = $stmt->fetch();

            $articles = getFromAPI($source['base_url'], $_ENV["{$source['api_key']}"]);
            foreach($articles as $article){
                $catId = $this->categoryModel->getCategoryId($article['category']);
                
                if($catId === -1){
                    $catId = $this->categoryModel->createCategory($article['category'])['category_id'];
                }
                
                $normalised = [
                    'source_id' => $source['id'],
                    'category_id' => $catId,
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'url' => $article['url'],
                    'image_url' => $article['urlToImage'],
                    'published_at' => $article['publishedAt'],
                    'author_name' => $article['author'],
                    'title_hash' => md5($article['title'])
                ];
                $this->addExternalArticle($normalised);
            }
        } catch(PDOException $e){}
    }
    
    /**
     * adds local articles to the database
     * @return id of inserted article, -1 if there is an error
     */
    public function createArticle($data) {
        try{
            $stmt = $this->pdo->prepare("
                INSERT INTO articles (author_id, category_id, title, slug, content, cover_image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $pdo = $this->pdo;
            $data['slug'] = SlugGenerator::makeUnique($data['title'], function($slug) use ($pdo) {
                $stmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
                $stmt->execute([$slug]);
                return $stmt->fetchColumn() > 0;
            });
            $res = $stmt->execute([
                $data['author_id'],
                $data['category_id'],
                $data['title'],
                $data['slug'],
                $data['content'],
                $data['cover_image_url'],
                $data['status']
            ]);
            
            return [
                'success' => $res ? true : false,
                'id' => $res ? $this->pdo->lastInsertId() : -1,
                'message' => $res ? 'Article Successfully Created' : 'Server Error'
            ];
        } catch(PDOException $e){
            error_log($e);
            log_("ArticleModel - createArticle " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Server Error'
            ];
        }
    }
    

    public function addExternalArticle($data){
        try{
            $params = ['source_id', 'category_id', 'title', 'content', 'url', 'image_url', 'published_at', 'author_name', 'title_hash'];
            $values = [$data['source_id'],$data['category_id'],$data['title'],$data['content'],$data['url'],$data['image_url'],$data['published_at'],$data['author_name'],$data['title_hash']];
            $placeHolders = ['?','?','?','?','?','?','?','?','?'];
            
            if(isset($data['external_id'])){
                $params[] = 'external_id';
                $values[] = $data['external_id'];
                $placeHolders[] = '?';
            }
            $sql = 'INSERT INTO external_articles ('. implode(',', $params) .') VALUES ('.implode(',', $placeHolders).')';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($values);

            return $res ? $this->pdo->lastInsertId() : -1;
        } catch(PDOException $e){
            error_log($e->getMessage());
            log_("addExternalArticle {$e->getMessage()}");
            return -1;
        }
    }
    
    // needs modification
    public function updateArticle($id, $data) {
        try{
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("
                UPDATE news 
                SET newsImg = ?, newsCategory = ?, newsTitle = ?, newsHeadline = ?, fullContent = ?, newsDate = ? 
                WHERE id = ?
            ");
            $cur_pos = ($this->getNewsById($id))['position'];
            $column = null;
            if($cur_pos === 'mpnews_slide'){
                $column = 'newsSlide';
            } else if($cur_pos === 'mpnews_fade'){
                $column = 'fadeNews';
            }
            $result = $stmt->execute([
                $data['newsImg'],
                $data['newsCategory'],
                $data['newsTitle'],
                '' . $data['newsHeadline'],
                $data['fullContent'],
                $data['newsDate'],
                $id
            ]);

            if($column){
                if(!$this->mainpagemodel->deleteItem($column, $id)){
                    if($this->pdo->inTransaction()) $this->pdo->rollback();
                     return false;
                };
            }
            if(isset($data['position'])){
                $this->mainpagemodel->addItem($data['position'], $id);
            }
            $this->pdo->commit();
            return $result;
        } catch(PDOException $e){
            error_log($e->getMessage());
            if($this->pdo->inTransaction()){
                $this->pdo->rollback();
            }
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
        } catch(PDOException $e){}
    }
}
?>