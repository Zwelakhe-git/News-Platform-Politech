<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Services\ArticleInteractionService;
use Thunderpc\Vkurse\Models\AdminModel;
use Thunderpc\Vkurse\Auth;
use Thunderpc\Vkurse\Utils\Log;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');
$dotenv->load();
Log::init();

class StatsApi{
    public function like($req, $res){
        try {
            
            if($_SESSION['user']['role'] === 'author' || $_SESSION['user']['role'] === 'admin'){
                $res->json([
                    'success' => false,
                    'message' => "Only readers can like"
                ]);
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return [
                    'success' => false,
                    'message' => 'Only POST allowed'
                ];
            }
            $apiKey = $req->query->key;
            
            if(!$this->allow($apiKey)){
                return $res->status(200)->json([
                    'success' => false,
                    'message' => 'invalid api key'
                ]);
            }

            $userId = $_SESSION['user']['id'] ?? (int)($req->body['user_id'] ?? 0);
            $articleId = (int)($req->params->id ?? 0);

            if ($userId <= 0 || $articleId <= 0) {
                return $res->json([
                    'success' => false,
                    'message' => 'user_id and article_id are required'
                ]);
            }
            if((bool)($data['like'] ?? false)){
                $result = (new ArticleInteractionService())->likeArticle($articleId, $userId);
                return $res->json($result);
            }
        } catch(\Exception $e){
            Log::error("Error in like api");
            return $res->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }

    public function comment($req, $res){
        try {
            //$data = json_decode(file_get_contents('php://input'), true) ?: [];
            $userId = $_SESSION['user']['id'] ?? (int)($req->body['user_id'] ?? 0);
            $articleId = (int)($req->params->id ?? 0);
            $content = trim((string)($req->body->content ?? ''));

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $result = (new ArticleInteractionService())->getAllComments($articleId);
                return $res->status(200)->json($result);
            } else if($_SERVER['REQUEST_METHOD' !== 'POST']){
                return $res->json([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
            }
            if ($userId <= 0 || $articleId <= 0 || $content === '') {
                return $res->json([
                    'success' => true,
                    'message' => 'user_id, post_id and content are required'
                ]);
            }
            
            if(!$this->acceptAuthToken()){
                Log::warn("Auth token not accepted");
                /*return $res->status(401)->json([
                    'success' => false,
                    'message' => 'Токен не предоставлен'
                ]);*/
            }
            
            if(!$this->allow($apiKey)){
                return $res->status(200)->json([
                    'success' => false,
                    'message' => 'invalid api key'
                ]);
            }

            $result = (new ArticleInteractionService())->commentOnArticle($articleId, $userId, $content);
        } catch(\Exception $e){
            Log::error("Error in comment: {$e->getMessage()}");
            return $res->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
        return $res->json([
            'success' => false,
            'message' => 'Unknown error'
        ]);

    }

    private function allow($key){
        try {
            return (new AdminiModel())->acceptAPIKey($apiKey);
        } catch(\Exception $e){
            return false;
        }
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