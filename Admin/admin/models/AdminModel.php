<?php
namespace Thunderpc\Vkurse\Admin\Models;

require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;
use Thunderpc\Vkurse\Admin\Models\UserModel;
use Thunderpc\Vkurse\Admin\Models\ArticleModel;
use Dotenv\Dotenv;

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
                                        id,full_name,password_hash FROM users WHERE full_name = ? AND email = ? LIMIT 1");

            $stmt->execute([
                $data['full_name'],
                $data['email']
            ]);
            $result = $stmt->fetch();

            if($result == false){
                return ['success' => false, "message" => "invalid login or password"];
            }
            if($result['status'] === 'unregistered') return ['success' => false, 'message' => 'unregistered'];

            if(password_verify($data['password'], $result['password_hash'])){
                $this->userModel->updateLoginTime($data['full_name'], $data['email']);
                return ['success' => true, 'message' => 'login successful'];
            }

            return ['success' => false, 'message' => 'wrong password'];
        } catch (PDOException $e){
            log_("AdminModel grantLogin " . $e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        } catch(Exception $e){
            log_("AdminModel grantLogin " . $e->getMessage());
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
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

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

    public function createAPIKey(){
        try{
            $user = $this->userModel->getUserDetails($_SESSION['name'], $_SESSION['email']);
            if(!$user){}

            $userId = $user['id'];
            $sql = 'INSERT INTO api_keys ()';
            $stmt = $this->pdo->query($sql);
            return $stmt->execute();
        } catch(PDOException $e){}
    }
    
    public function acceptAPIKey($apiKey){
        try{
            $sql = 'SELECT * FROM api_keys WHERE api_key = ?';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$apiKey]);
            $res = $stmt->fetch();

            return $res['status'] === 'valid' ? true : false;
        } catch(PDOException $e){
            error_log($e->getMessage());
            return false;
        }
    }
    public function blockUser($name, $email){}

    public function cancelUserSubscription(){}

    public function addEmailSubscriber(){}

    public function addExternalNewsSource($data){
        try{
            file_put_contents(BASE_DIR . '/.env', "{$data['name']}_api_key={$data['api_key']}" . PHP_EOL, FILE_APPEND);
            $_ENV["{$data['name']}_api_key"] = $data['api_key'];
            $sql = 'INSERT IGNORE INTO external_sources (name, base_url, api_key) VALUES (?,?,?)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$data['name'], $data['base_url'], "{$data['name']}_api_key"]);
            return [
                'success' => true
            ];
        } catch(Exception $e){
            error_log($e->getMessage());
            return [
                'success' => false
            ];
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
            log_('deleteExternalNewsSource ' . $e->getMessage());
        }
    }
}

?>