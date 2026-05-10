<?php 
namespace Thunderpc\Vkurse\Models;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\Database;
use Thunderpc\Vkurse\Utils\Log;

Log::init();
class UserModel extends Database{
    public function __construct(){
        parent::__construct();
    }

    public function getAllUsers(){
        try {
            $sql = "SELECT id, full_name, email, is_blocked, role, avatar_url FROM users";
            $stmt = $this->pdo->query($sql);
            $users = $stmt->fetchAll();
            foreach($users as $user){
                $stmt = $this->pdo->prepare("SELECT  plan, status, started_at, expires_at, auto_renew FROM subscriptions WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $subscriptions = $stmt->fetchAll();
                if(!empty($subscriptions)){
                    $user['subscriptions'] = $subscriptions;
                }
            }
            return $users;
        } catch(\PDOException $e){
            Log::error("PDOEexception: {$e->getMessage()}");
            return [];
        }
        return [];
    }
    
    public function createUser($data){
        try{
            $sql = 'INSERT INTO users (email, password_hash, full_name, role) VALUES (?,?,?,?)';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);

            if($res){
                $id = $this->pdo->lastInsertId();
                $cachePayload = [
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'avatar_url' => ''
                ];

                if(isset($data['avatar_url'])){
                    $this->updateUserAvatar(['avatar_url' => $data['avatar_url'], 'user_id' => $id]);
                    $cachePayload['avatar_url'] = $data['avatar_url'];
                }
                
                // Cache user data in Redis with 1 hour TTL
                $this->setCacheData("user:{$id}", $cachePayload);
            }
            
            return [
                'success' => $res ? true : false,
                'user_id' => $res ? $this->pdo->lastInsertId() : -1
            ];
        } catch(PDOException $e){
            Log::error($e->getMessage());
            return ['success' => false];
        } catch(Exception $e){
            Log::error($e->getMessage());
            return ['success' => false];
        }
    }

    public function deleteUser($id){
        try{
            $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ? LIMIT 1');
            $res = $stmt->execute([$id]);
            
            if($res){
                $this->invalidateCache("user:{$id}");
            }
            
            return $res ? true : false;
        } catch(PDOException $e){
            Log::error($e->getMessage());
            return false;
        } catch(Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function updateUserProfile($username, $currentPassword, $newName, $newEmail, $newAvatar, $newPassword) {
        try {
            $this->pdo->beginTransaction();

            // First, verify current password
            $stmt = $this->pdo->prepare("SELECT id, password_hash, email FROM users WHERE full_name = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['response' => 'fail', 'message' => 'User not found'];
            }

            if (!password_verify($currentPassword, $user['password_hash'])) {
                return ['response' => 'fail', 'message' => 'Current password is incorrect'];
            }

            // Check if new username or email already exists (if changed)
            if ($newName !== $username) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE full_name = ? AND id != ?");
                $stmt->execute([$newName, $user['id']]);
                if ($stmt->fetchColumn() > 0) {
                    return ['response' => 'fail', 'message' => 'Username already taken'];
                }
            }

            if ($newEmail && $newEmail !== $user['email']) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$newEmail, $user['id']]);
                if ($stmt->fetchColumn() > 0) {
                    return ['response' => 'fail', 'message' => 'Email already in use'];
                }
            }

            // Prepare update data
            $updateFields = ['name = ?', 'email = ?'];
            $updateParams = [$newName, $newEmail];

            // Add avatar if provided
            if ($newAvatar !== '') {
                $updateFields[] = 'avatar_url = ?';
                $updateParams[] = $newAvatar;
            }

            // Update password if provided
            if ($newPassword) {
                $updateFields[] = 'password_hash = ?';
                $updateParams[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            // Add ID parameter
            $updateParams[] = $user['id'];

            // Execute update
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($updateParams);

            

            $this->pdo->commit();
            
            // Invalidate cache
            $this->invalidateCache("user:{$user['id']}");

            return [
                'success' => true,
                'message' => 'Profile updated successfully',
                'name' => $newName,
                'email' => $newEmail,
                'avatar_url' => $newAvatar
            ];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        } catch(Exception $e){
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Log::error($e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        }
    }
    
    public function updateUserAvatar($data){
        try{
            $sql = "UPDATE users SET avatar_url = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);

            if($stmt->execute([$data['avatar_url'], $data['user_id']])){
                $this->invalidateCache("user:{$data['user_id']}");
                return ["success" => true, "message" => "profile updated successfully"];
            }
            return ["success" => false, "message" => "failed to update profile"];
        } catch (Exception $e){
            Log::error($e->getMessage());
            return ["success" => false, "message" => "server error"];
        }
    }
    public function updateLoginTime($name, $email){
        try{
            $sql = 'UPDATE users SET last_login = CURRENT_TIMESTAMP() WHERE full_name = ? AND email = ?';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $email]);
        } catch(Throwable $e){
            Log::error($e->getMessage());
        }
    }
    

    /**
     * gives the id of a user, registered as an author.
     * @param name - username
     * @param email - user's email
     * @return (id, full_name, email, avatar_url, role)
     */
    public function getUserDetails($name, $email) {
        try {
            
            
            $stmt = $this->pdo->prepare("SELECT id, full_name, email, avatar_url, role, is_blocked FROM users WHERE full_name = ? AND email = ? LIMIT 1");
            $stmt->execute([$name, $email]);
            $user = $stmt->fetch();

            if(empty($user)) return [];
            
            $sql = "SELECT * FROM subscriptions WHERE user_id = {$user['id']} ORDER BY started_at DESC LIMIT 1";
            $stmt = $this->pdo->query($sql);

            // assign the latest
            $subscription = $stmt->fetch();
            if(!empty($subscription)){
                $user['subscription'] = $subscription;
            }

            $stmt = $this->pdo->query("SELECT * FROM notifications WHERE user_id = {$user['id']}");
            $notifications = $stmt->fetchAll();
            $stmt = $this->pdo->query("SELECT urh.read_at, art.title FROM user_reading_history urh
            LEFT JOIN articles art ON art.id = urh.article_id WHERE user_id = {$user['id']} ORDER BY read_at DESC LIMIT 10");
            $readHistory = $stmt->fetchAll();

            $user['read_history'] = $readHistory;
            $user['notifications'] = $notifications;

            if($user['role'] === 'author'){
                $stmt = $this->pdo->query("SELECT * FROM articles WHERE author_id = {$user['id']}");
                $articles = $stmt->fetchAll();
                $stmt = $this->pdo->query("SELECT COUNT(*) FROM author_followers WHERE author_id = {$user['id']}");
                $followersCount = $stmt->fetchColumn();

                $user['articles'] = $articles;
                $user['articles_count'] =  empty($articles) ? 0 : count($articles);
                $user['followers_count'] = $followersCount;

            } else {
                $stmt = $this->pdo->query("SELECT af.author_id AS id, u.full_name FROM author_followers af
                LEFT JOIN users u ON af.author_id = u.id WHERE af.follower_id = {$user['id']}");
                $authors = $stmt->fetchAll();
                if(!empty($authors)){
                    $user['followed_authors'] = [];
                    foreach($authors as $author){
                        $columns = ['art.id', 'c.name AS category', 'art.title',
                                    'art.slug', 'art.cover_image_url', 'art.status', 'art.views_count',
                                    'art.likes_count', 'art.comments_count', 'art.published_at'];

                        $stmt = $this->pdo->query("SELECT ". implode(',', $columns) ." FROM articles art LEFT JOIN categories c ON c.id = art.category_id WHERE art.author_id = {$author['id']}");
                        $articles = $stmt->fetchAll();

                        $user['followed_authors'][] = [
                            'full_name' => $author['full_name'],
                            'articles' => $articles
                        ];
                    }
                    
                    
                }
            }

            // Cache the user data for 1 hour
            $this->setCacheData($cacheKey, $user);
            
            return $user;

        } catch (PDOException $e) {
            Log::error($e->getMessage());
            return null;
        } catch(Exception $e){
            Log::error($e->getMessage());
            return null;
        }
    }

    public function getAuthorId($name, $email){
        /**
         * gives the id of a user, registered as an author.
         * @param name - username
         * @param email - user's email
         * @return -1 if the user doesnt have the author role, or if the user doesnt exist, else the user id
         */
        try{
            $sql = 'SELECT id FROM users WHERE full_name = ? AND email = ? AND role = "author"';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute([$name, $email]);
            if(!$res){
                return -1;
            }
            return $stmt->fetchColumn();
        } catch(\PDOException $e){
            Log::error($e->getMessage());
            return -1;
        }
    }

    /**
     * Приватный метод для безопасного получения данных из Redis
     * Не выбрасывает исключения, логирует ошибки
     * @return array|null Декодированные данные или null
     */
    private function getCachedData($key) {
        if (!isset($this->redisClient)) {
            return null;
        }
        
        try {
            $cached = $this->redisClient->get($key);
            return $cached !== false ? json_decode($cached, true) : null;
        } catch (\RedisException $e) {
            Log::error("Redis read error for key {$key}: {$e->getMessage()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Cache read error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Приватный метод для безопасного сохранения данных в Redis
     * Не выбрасывает исключения, логирует ошибки
     */
    private function setCacheData($key, $data, $ttl = 3600) {
        if (!isset($this->redisClient)) {
            return false;
        }
        
        try {
            $this->redisClient->set($key, json_encode($data, JSON_UNESCAPED_UNICODE));
            $this->redisClient->expire($key, $ttl);
            return true;
        } catch (\RedisException $e) {
            Log::error("Redis write error for key {$key}: {$e->getMessage()}");
            return false;
        } catch (\Exception $e) {
            Log::error("Cache write error: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Приватный метод для безопасной инвалидации кеша
     * Не выбрасывает исключения, логирует ошибки
     */
    private function invalidateCache($key) {
        if (!isset($this->redisClient)) {
            return false;
        }
        
        try {
            $this->redisClient->del($key);
            return true;
        } catch (\RedisException $e) {
            Log::error("Redis delete error for key {$key}: {$e->getMessage()}");
            return false;
        } catch (\Exception $e) {
            Log::error("Cache invalidation error: {$e->getMessage()}");
            return false;
        }
    }
} 
?>