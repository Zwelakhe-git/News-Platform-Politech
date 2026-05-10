<?php
namespace Thunderpc\Vkurse\Api;

require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\AdminModel;
use Thunderpc\Vkurse\Models\UserModel;
use Thunderpc\Vkurse\Models\ArticleModel;
use Thunderpc\Vkurse\Utils\Log;

Log::init();

class AdminApi {
    
    private $adminModel;
    private $userModel;
    private $articleModel;
    
    public function __construct() {
        $this->adminModel = new AdminModel();
        $this->userModel = new UserModel();
        $this->articleModel = new ArticleModel();
    }
    
    // ==================== USER MANAGEMENT ====================
    
    /**
     * GET /api/admin/users
     * Получить всех пользователей
     * Query params: limit=50, offset=0
     */
    public function getUsers($req, $res) {
        try {
            $limit = (int)($req->query->limit ?? 50);
            $offset = (int)($req->query->offset ?? 0);
            
            $users = $this->adminModel->getAllUsers($limit, $offset);
            
            return $res->status(200)->json([
                'success' => true,
                'count' => count($users),
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getUsers: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        } catch(\Throwable $e){
            Log::error("Error in getUsers: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/users/{id}/block
     * Заблокировать пользователя
     */
    public function blockUser($req, $res) {
        try {
            $userId = $req->params->id ?? null;
            $reason = $req->body->reason ?? '';
            
            if (!$userId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ]);
            }
            
            $result = $this->adminModel->blockUser($userId, $reason);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'User blocked successfully',
                    'user_id' => $userId
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in blockUser: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/users/{id}/unblock
     * Разблокировать пользователя
     */
    public function unblockUser($req, $res) {
        try {
            $userId = $req->params->id ?? null;
            
            if (!$userId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ]);
            }
            
            $result = $this->adminModel->unblockUser($userId);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'User unblocked successfully',
                    'user_id' => $userId
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in unblockUser: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/users/{id}/promote
     * Назначить пользователя автором
     */
    public function promoteUser($req, $res) {
        try {
            $userId = $req->params->id ?? null;
            $role = $req->body->role ?? 'author'; // author, moderator, admin
            
            if (!$userId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ]);
            }
            
            $validRoles = ['author', 'moderator', 'admin'];
            if (!in_array($role, $validRoles)) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Invalid role'
                ]);
            }
            
            $result = $this->adminModel->promoteUser($userId, $role);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => "User promoted to {$role} successfully",
                    'user_id' => $userId,
                    'new_role' => $role
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in promoteUser: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    // ==================== ARTICLE MODERATION ====================
    
    /**
     * GET /api/admin/articles/moderation
     * Получить статьи на модерацию
     */
    public function getArticlesForModeration($req, $res) {
        try {
            $status = $req->query->status ?? 'pending'; // pending, draft
            $limit = (int)($req->query->limit ?? 20);
            $offset = (int)($req->query->offset ?? 0);
            
            $articles = $this->articleModel->getArticlesForModeration($status, $limit, $offset);
            
            return $res->status(200)->json([
                'success' => true,
                'count' => count($articles),
                'status' => $status,
                'limit' => $limit,
                'offset' => $offset,
                'articles' => $articles
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getArticlesForModeration: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/articles/{id}/approve
     * Одобрить статью
     */
    public function approveArticle($req, $res) {
        try {
            $articleId = $req->params->id ?? null;
            $moderatorId = $req->user['id'] ?? null;
            
            if (!$articleId || !$moderatorId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Article ID and moderator auth required'
                ]);
            }
            
            $result = $this->articleModel->approveArticle($articleId, $moderatorId);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'Article approved successfully',
                    'article_id' => $articleId
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in approveArticle: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/articles/{id}/reject
     * Отклонить статью
     */
    public function rejectArticle($req, $res) {
        try {
            $articleId = $req->params->id ?? null;
            $moderatorId = $req->user['id'] ?? null;
            $reason = $req->body['reason'] ?? 'No reason provided';
            
            if (!$articleId || !$moderatorId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Article ID and moderator auth required'
                ]);
            }
            
            $result = $this->articleModel->rejectArticle($articleId, $moderatorId, $reason);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'Article rejected successfully',
                    'article_id' => $articleId
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in rejectArticle: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    // ==================== EXTERNAL SOURCES MANAGEMENT ====================
    
    /**
     * GET /api/admin/sources
     * Получить все внешние источники
     */
    public function getSources($req, $res) {
        try {
            $limit = (int)($req->query->limit ?? 50);
            $offset = (int)($req->query->offset ?? 0);
            
            $sources = $this->adminModel->getSources($limit, $offset);
            
            return $res->status(200)->json([
                'success' => true,
                'count' => count($sources),
                'sources' => $sources
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getSources: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/sources
     * Добавить новый источник
     */
    public function createSource($req, $res) {
        try {
            $name = $req->body->name ?? null;
            $baseUrl = $req->body->base_url ?? null;
            $apiKey = $req->body->api_key ?? null;
            $updateInterval = (int)($req->body->update_interval_minutes ?? 30);
            
            // Валидация
            if (!$name || !$baseUrl) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Name and base_url are required'
                ]);
            }
            
            $data = [
                'name' => $name,
                'base_url' => $baseUrl,
                'api_key' => $apiKey,
                'update_interval_minutes' => $updateInterval,
                'is_active' => true
            ];
            
            $result = $this->adminModel->addExternalNewsSource($data);
            
            if ($result['success']) {
                return $res->status(201)->json([
                    'success' => true,
                    'message' => 'Source created successfully',
                    'source_id' => $result['source_id'] ?? null
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in createSource: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/sources/{id}/toggle
     * Включить/выключить источник
     */
    public function toggleSource($req, $res) {
        try {
            $sourceId = $req->params->id ?? null;
            
            if (!$sourceId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Source ID is required'
                ]);
            }
            
            $result = $this->adminModel->toggleSource($sourceId);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'Source toggled successfully',
                    'source_id' => $sourceId,
                    'is_active' => $result['is_active'] ?? null
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in toggleSource: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * DELETE /api/admin/sources/{id}
     * Удалить источник
     */
    public function deleteSource($req, $res) {
        try {
            $sourceId = $req->params->id ?? null;
            
            if (!$sourceId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Source ID is required'
                ]);
            }
            
            $result = $this->adminModel->deleteSource($sourceId);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'Source deleted successfully',
                    'source_id' => $sourceId
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in deleteSource: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    /**
     * POST /api/admin/sources/{id}/sync
     * Синхронизировать источник (получить новые статьи)
     */
    public function syncSource($req, $res) {
        try {
            $sourceId = $req->params->id ?? null;
            
            if (!$sourceId) {
                return $res->status(400)->json([
                    'success' => false,
                    'message' => 'Source ID is required'
                ]);
            }
            
            // Можно запустить async task или выполнить синхронно
            $result = $this->adminModel->syncSource($sourceId);
            
            if ($result['success']) {
                return $res->status(200)->json([
                    'success' => true,
                    'message' => 'Source synced successfully',
                    'source_id' => $sourceId,
                    'articles_synced' => $result['articles_synced'] ?? 0
                ]);
            }
            
            return $res->status(400)->json($result);
        } catch (\Exception $e) {
            Log::error("Error in syncSource: {$e->getMessage()}");
            return $res->status(500)->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
}
?>