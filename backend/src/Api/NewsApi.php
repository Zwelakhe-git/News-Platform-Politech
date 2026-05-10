<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\ArticleModel;
use Thunderpc\Vkurse\Models\AdminModel;
use Thunderpc\Vkurse\Auth\Auth;
use Thunderpc\Vkurse\Services\ArticleSearchService;
use Thunderpc\Vkurse\Utils\Log;

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');
$dotenv->load();
Log::init();

/**
 * response format:
 * {
 * 'success': bool,
 * 'message': string,
 * 'data': {
 *  'count': int,
 *  'category': string,
 *  'source': string
 *  'date': timestamp,
 *  'articles': array
 * }
 * }
 */
class NewsApi{
    private $adminModel;
    private $model;
    public function __construct(){
        $this->adminModel = new AdminModel();
        $this->model = new ArticleModel();
        Log::info('NewsApi constructor called');
    }
    
    public function getArticles($req, $res){
        Log::info('calling getArticles');
        $cat = $req->query->category;
        $apiKey = $req->query->key;
        $quantity = $req->query->limit;
        //Log::info("received params: $cat, $apiKey, $quantity");

        if(!$this->adminModel->acceptAPIKey($apiKey)){
            $res->status(200)->json([
                'success' => false,
                'message' => 'invalid api key'
            ]);
            exit;
        }
        if(isset($req->query->aid)){
            $articles = $this->model->getArticlesByAuthor($req->query->aid);
            $res->status(200)->json([
                'success'=> true,
                'date'=> date('Y-m-d H:i:s'),
                'data'=> [
                    'count'=> count($articles),
                    'articles'=> $articles
                ]
            ]);
            exit;
        }
        //Log::info("No error in admin model");
        $source = 'external';
        $articles = $this->model->getAllArticles($source);
        if($quantity !== 'all'){
            $articles = array_slice($articles, 0, $quantity);
        }

        //$res->json(array_splice($res, 0, $quantity));
        $res->status(200)->json([
            'success'=> true,
            'date'=> date('Y-m-d H:i:s'),
            'data'=> [
                'count'=> count($articles),
                'category' => $cat,
                'articles'=> $articles
            ]
        ]);
    }

    public function rejectArticle($req, $res){
        if($_SESSION['user']['role'] !== 'admin'){}
    }

    private function acceptAuthToken(): bool{
        try {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';

            if(!$authHeader){
                Log::warn('Authorization header not set');
                return false;
            }
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $jwt = $matches[1];
            } else {
                Log::warn("Invalid auth token");
                return false;
            }

            $secretKey = $_ENV['JWT_SECRET'];

             // 2. Верифицируем токен
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            return $decoded ? true : false;
        } catch(\Exception $e){
            Log::error("Error validating auth token");
            return false;
        }
        return false;
    }
}

?>