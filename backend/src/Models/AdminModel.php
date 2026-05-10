<?php
namespace Thunderpc\Vkurse\Models;

require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\Database;
use Thunderpc\Vkurse\Models\UserModel;
use Thunderpc\Vkurse\Models\ArticleModel;
use Dotenv\Dotenv;
use Thunderpc\Vkurse\Utils\Log;

Log::init();

$dotenv =Dotenv::createImmutable(BASE_DIR, '.env');

class AdminModel extends Database {
    private $userModel;
    private $articleModel;
    public function __construct(){
        parent::__construct();
        $this->userModel = new UserModel();
        $this->articleModel = new ArticleModel();
    }
    public function grantLogin($data){
        try{
            $stmt = $this->pdo->prepare("SELECT CASE WHEN full_name IS NULL THEN 'unregistered' ELSE 'registered' END AS status,
                                        id,full_name,password_hash, is_blocked FROM users WHERE full_name = ? AND email = ? LIMIT 1");

            $stmt->execute([
                $data['full_name'],
                $data['email']
            ]);
            $result = $stmt->fetch();

            if($result == false){
                return ['success' => false, "message" => "invalid login or password"];
            }
            if($result['is_blocked']) return ['success' => false, 'message' => 'Ваш аккаунт заблокирован'];
            if($result['status'] === 'unregistered') return ['success' => false, 'message' => 'unregistered'];

            if(password_verify($data['password'], $result['password_hash'])){
                $this->userModel->updateLoginTime($data['full_name'], $data['email']);
                return ['success' => true, 'message' => 'login successful'];
            }

            return ['success' => false, 'message' => 'wrong password'];
        } catch (PDOException $e){
            Log::error("AdminModel - grantLogin - " . $e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        } catch(Exception $e){
            Log::error("AdminModel - grantLogin - " . $e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        }
    }

    public function registerUser($data){
        try{
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE full_name = ? OR email = ?");
            $stmt->execute([$data['full_name'], $data['email']]);
            $user_count = $stmt->fetchColumn();
            
            if ($user_count > 0) {                
                // Проверяем что именно существует
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE full_name = ?");
                $stmt->execute([$data['full_name']]);
                $message = '';
                if ($stmt->fetchColumn() > 0) {
                    $message = 'Username already exists';
                } else {
                    $message = 'Email already exists';
                }
                return ['success' => false, 'message' => $message];
            }
            $params = ['email', 'password_hash', 'full_name', 'role'];
            $values = [$data['email'],$data['password_hash'],$data['full_name'],$data['role']];
            $placeholders = ['?','?','?','?'];
            
            if(isset($data['avatar_url'])){
                $params[] = 'avatar_url';
                $values[] = $data['avatar_url'];
                $placeholders[] = '?';
            }
            
            $stmt = $this->pdo->prepare('INSERT INTO users ('.implode(',', $params).') VALUES ('.implode(',', $placeholders).')');
            $result = $stmt->execute($values);

            if($result){
                return ['success' => true, 'message' => 'registration successful'];
            }
            return ['success' => false, 'message' => 'server error'];

        } catch(PDOException $e){
            Log::error("AdminModel - registerUser - {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * needs update
     */
    public function getUserActivity($username){
        try{
            $sql = "SELECT * FROM UserActivityLog WHERE username = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$username]);
            $rows = $stmt->fetchAll();
            if(count($rows) > 0){
                return [
                    "success" => true,
                    "data" => $rows
                ];
            } else {
                return ["success" => false, "message" => "no activities"];
            }
        } catch(PDOException $e){
            error_log($e->getMessage());
            return ["success" => false, "message" => "server error here"];
        }
    }

    /**
     * generates a unique api key and stores the hash in the database,
     * @return array containing the original api key
     */
    public function createAPIKey(){
        try{
            $user = $this->userModel->getUserDetails($_SESSION['user']['full_name'], $_SESSION['user']['email']);
            if(!$user){
                return [
                    'success' => false,
                    'message' => 'user not registered'
                ];
            }
            if($user['is_blocked']){
                return [
                    'success' => false,
                    'message' => 'User is blocked'
                ];
            }

            $userId = $user['id'];
            $key = preg_replace('/\W/', '_', uniqid('vkurse_', true));
            $hash = md5($key);
            $sql = 'INSERT INTO api_keys (user_id, api_key) VALUES (?,?)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $hash]);

            return [
                'success' => true,
                'api_key' => $key
            ];
        } catch(PDOException $e){
            Log::error("AdminModel - createAPIKey - {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Server Error'
            ];
        }
    }
    
    public function getApiKeyOwnerId($apiKey){
        try{
            $stmt = $this->pdo->prepare("SELECT user_id FROM api_keys WHERE api_key = ?");
            $stmt->execute([md5($apiKey)]);
            return $stmt->fetchColumn();
        } catch(PDOException $e){
            Log::error($e->getMessage());
        } catch(Exception $e){
            Log::error($e->getMessage());
        }
        return -1;
    }
    
    public function acceptAPIKey($apiKey){
        try{
            //Log::info("calling AdminModel acceptAPIKey");
            $sql = 'SELECT * FROM api_keys WHERE api_key = ? AND status = "active"';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([md5($apiKey)]);
            $res = $stmt->fetch();

            //Log::info("request completed without errors ");
            return !empty($res);
        } catch(PDOException $e){
            Log::error("AdminModel - acceptAPIKey - {$e->getMessage()}");
            return false;
        } catch(Exception $e){
            Log::error("AdminModel - acceptAPIKey - {$e->getMessage()}");
            return false;
        }
    }

    public function cancelUserSubscription(){}

    public function addEmailSubscriber(){}

    /**
     * creates and saves an environment variable with the source api
     * and saves the variable into the database
     * @return array with success flag
     */
    public function addExternalNewsSource($data){
        try{
            if(!$this->envVariableExists("{$data['name']}_api_key")){
                file_put_contents(BASE_DIR . '/.env', "{$data['name']}_api_key={$data['api_key']}" . PHP_EOL, FILE_APPEND);
                $_ENV["{$data['name']}_api_key"] = $data['api_key'];
            }
            $columns = ['name', 'base_url', 'api_key'];
            $placeholders = "?,?,?";
            $values = [$data['name'], $data['base_url'], "{$data['name']}_api_key"];

            if(isset($data['update_interval_minutes'])){
                $columns[] = 'update_interval_minutes';
                $placeholders .= ",?";
                $values[] = $data['update_interval_minutes'];
            }
            if(isset($data['is_active'])){
                $columns[] = 'is_active';
                $placeholders .= ",?";
                $values[] = $data['is_active'];
            }
            $sql = "INSERT IGNORE INTO external_sources " . implode(', ', $columns) . " VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return [
                'success' => true, 'source_id' => $this->pdo->lastInsertId()
            ];
        } catch(Exception $e){
            Log::error("AdminModel - addExternalNewsSource - {$e->getMessage()}");
            return [
                'success' => false, 'message' => 'Server error'
            ];
        }
    }

    public function envVariableExists($varName){
        try{
            $data = file_get_contents(BASE_DIR . '/.env');
            $lines = explode("\n", $data);
            foreach($lines as $line){
                if(empty($line) || !strpos($line,'=')) continue;
                $pair = explode('=', $line);

                if(count($pair) === 0) continue;
                if(strcmp($varName, $pair[0]) === 0 && !empty($pair[1])){
                    return true;
                }
            }
            return false;
        } catch(Exception $e){
            Log::error("findEnvVariable - {$e->getMessage()}");
            return false;
        }
    }


    /**
     * forcefully deletes a news source, removing related articles.
     * models like comments, user_likes read from external_articles but they wont restrict
     * deletions.
     */
    public function forceDeleteExternalNewsSource($name, $baseUrl){
        try{
            $sql = 'SELECT id FROM external_sources WHERE name = ? AND base_url = ?';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $baseUrl]);
            $res = $stmt->fetch();

            if(empty($res)){
                return [
                    'success' => false,
                    'message' => 'Source does not exist'
                ];
            }

            $count = $this->articleModel->deleteExternalArticles($res['id']);
            $stmt = $this->pdo->prepare('DELETE FROM external_sources WHERE name = ? AND base_url = ?');
            $stmt->execute([$name, $baseUrl]);
            return [
                'success' => true,
                'message' => "source $name ($baseUrl) successfully deleted, along with $count articles"
            ];
        } catch(Exception $e){
            Log::error('deleteExternalNewsSource ' . $e->getMessage());
        }
    }
    public function getAllExternalSources(){
        try{
            $stmt = $this->pdo->query("SELECT * FROM external_sources");
            return $stmt->fetchAll();
        } catch(PDOException $e){}
    }

    // ==================== USER MANAGEMENT ====================

    /**
     * Получить всех пользователей
     */
    public function getAllUsers($limit = 50, $offset = 0){
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, full_name, email, role, is_blocked, created_at, last_login 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Log::error("getAllUsers error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Заблокировать пользователя
     */
    public function blockUser($userId, $reason = ''){
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET is_blocked = true WHERE id = ?");
            $res = $stmt->execute([$userId]);
            
            if ($res) {
                // Можно добавить логирование в отдельную таблицу если нужно
                Log::info("User $userId blocked. Reason: $reason");
                return ['success' => true, 'message' => 'User blocked successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to block user'];
        } catch (PDOException $e) {
            Log::error("blockUser error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * Разблокировать пользователя
     */
    public function unblockUser($userId){
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET is_blocked = false WHERE id = ?");
            $res = $stmt->execute([$userId]);
            
            if ($res) {
                Log::info("User $userId unblocked");
                return ['success' => true, 'message' => 'User unblocked successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to unblock user'];
        } catch (PDOException $e) {
            Log::error("unblockUser error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * Назначить пользователя автором/модератором/админом
     */
    public function promoteUser($userId, $role = 'author'){
        try {
            $validRoles = ['author', 'moderator', 'admin'];
            if (!in_array($role, $validRoles)) {
                return ['success' => false, 'message' => 'Invalid role'];
            }
            
            $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $res = $stmt->execute([$role, $userId]);
            
            if ($res) {
                Log::info("User $userId promoted to $role");
                return ['success' => true, 'message' => 'User promoted successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to promote user'];
        } catch (PDOException $e) {
            Log::error("promoteUser error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    // ==================== SOURCES MANAGEMENT ====================

    /**
     * Получить все внешние источники
     */
    public function getSources($limit = 50, $offset = 0){
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, base_url, is_active, update_interval_minutes, last_sync_at 
                FROM external_sources 
                ORDER BY last_sync_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Log::error("getSources error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Создать новый источник
     */
    public function createSource($data){
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO external_sources (name, base_url, api_key, update_interval_minutes, is_active) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $data['name'] ?? null,
                $data['base_url'] ?? null,
                $data['api_key'] ?? null,
                $data['update_interval_minutes'] ?? 30,
                $data['is_active'] ?? true
            ]);
            
            if ($res) {
                $sourceId = $this->pdo->lastInsertId();
                Log::info("Source created with ID: $sourceId");
                return ['success' => true, 'source_id' => $sourceId];
            }
            
            return ['success' => false, 'message' => 'Failed to create source'];
        } catch (PDOException $e) {
            Log::error("createSource error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * Включить/выключить источник
     */
    public function toggleSource($sourceId){
        try {
            // Сначала получаем текущее значение
            $stmt = $this->pdo->prepare("SELECT is_active FROM external_sources WHERE id = ?");
            $stmt->execute([$sourceId]);
            $source = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$source) {
                return ['success' => false, 'message' => 'Source not found'];
            }
            
            $newStatus = !$source['is_active'];
            $stmt = $this->pdo->prepare("UPDATE external_sources SET is_active = ? WHERE id = ?");
            $res = $stmt->execute([$newStatus, $sourceId]);
            
            if ($res) {
                Log::info("Source $sourceId toggled to " . ($newStatus ? 'active' : 'inactive'));
                return ['success' => true, 'is_active' => $newStatus];
            }
            
            return ['success' => false, 'message' => 'Failed to toggle source'];
        } catch (PDOException $e) {
            Log::error("toggleSource error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * Удалить источник
     */
    public function deleteSource($sourceId){
        try {
            $stmt = $this->pdo->prepare("SELECT name FROM external_sources WHERE id = ?");
            $stmt->execute([$sourceId]);
            $source = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$source) {
                return ['success' => false, 'message' => 'Source not found'];
            }
            
            // Удаляем статьи источника
            $stmt = $this->pdo->prepare("DELETE FROM external_articles WHERE source_id = ?");
            $stmt->execute([$sourceId]);
            
            // Удаляем сам источник
            $stmt = $this->pdo->prepare("DELETE FROM external_sources WHERE id = ?");
            $res = $stmt->execute([$sourceId]);
            
            if ($res) {
                Log::info("Source $sourceId ({$source['name']}) deleted");
                return ['success' => true, 'message' => 'Source deleted successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to delete source'];
        } catch (PDOException $e) {
            Log::error("deleteSource error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    /**
     * Синхронизировать источник (получить новые статьи)
     */
    public function syncSource($sourceId){
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM external_sources WHERE id = ?");
            $stmt->execute([$sourceId]);
            $source = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$source) {
                return ['success' => false, 'message' => 'Source not found'];
            }
            
            if (!$source['is_active']) {
                return ['success' => false, 'message' => 'Source is not active'];
            }
            
            // TODO: Реализовать логику синхронизации с внешним источником
            // Это может быть асинхронная задача
            $articlesCount = 0; // Количество синхронизированных статей
            
            // Обновляем время последней синхронизации
            $stmt = $this->pdo->prepare("UPDATE external_sources SET last_sync_at = NOW() WHERE id = ?");
            $stmt->execute([$sourceId]);
            
            Log::info("Source $sourceId synced. Articles added: $articlesCount");
            return [
                'success' => true,
                'message' => 'Source synced successfully',
                'articles_synced' => $articlesCount
            ];
        } catch (PDOException $e) {
            Log::error("syncSource error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Server error'];
        }
    }
}

?>