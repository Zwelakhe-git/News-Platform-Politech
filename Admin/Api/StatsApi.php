<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Services\ArticleInteractionService;
use Thunderpc\Vkurse\Admin\Models\AdminModel;
use Thunderpc\Vkurse\Auth\Auth;
use Thunderpc\Vkurse\Admin\Utils\Log;

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
                $res->status(200)->json([
                    'success' => false,
                    'message' => 'invalid api key'
                ]);
                exit;
            }

            // $data = json_decode(file_get_contents('php://input'), true) ?: [];
            $userId = $_SESSION['user']['id'] ?? (int)($req->body['user_id'] ?? 0);
            $articleId = (int)($req->params->id ?? 0);

            if ($userId <= 0 || $articleId <= 0) {
                $res->json([
                    'success' => false,
                    'message' => 'user_id and article_id are required'
                ]);
            }
            if((bool)($data['like'] ?? false)){
                $result = (new ArticleInteractionService())->likeArticle($articleId, $userId);
                $res->json($result);
            }
        } catch(\Exception $e){
            Log::error("Error in like api");
            $res->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }

    public function comment($req, $res){
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $res->json([
                    'success' => false,
                    'message' => 'Only POST allowed'
                ]);
                exit;
            }

            if(!$this->allow($apiKey)){
                $res->status(200)->json([
                    'success' => false,
                    'message' => 'invalid api key'
                ]);
                exit;
            }
            //$data = json_decode(file_get_contents('php://input'), true) ?: [];
            $userId = $_SESSION['user']['id'] ?? (int)($req->body['user_id'] ?? 0);
            $articleId = (int)($req->params->id ?? 0);
            $content = trim((string)($req->body->content ?? ''));

            if ($userId <= 0 || $articleId <= 0 || $content === '') {
                $res->json([
                    'success' => true,
                    'message' => 'user_id, post_id and content are required'
                ]);
                exit;
            }

            $result = (new ArticleInteractionService())->commentOnArticle($articleId, $userId, $content);
        } catch(\Exception $e){
            Log::error("Error in comment: {$e->getMessage()}");
            $res->json([
                'success' => false,
                'message' => 'Server error'
            ]);
            exit;
        }
        $res->json([
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
}
?>