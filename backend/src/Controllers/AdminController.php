<?php
namespace Thunderpc\Vkurse\Controllers;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\ArticleModel;
use Thunderpc\Vkurse\Models\UserModel;
use Thunderpc\Vkurse\Models\AdminModel;
use Thunderpc\Vkurse\Auth\Auth;
use Thunderpc\Vkurse\Utils\Log;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');
$dotenv->load();

Log::init();

class AdminController {
    private $articleModel;
    private $userModel;
    private $auth;
    private $model;
    private $debugFile = __DIR__ . '/../../logs/log.log';
    
    public function __construct() {
        $this->articleModel = new ArticleModel();
        $this->userModel = new UserModel();
        $this->auth = new Auth();
        $this->model = new AdminModel();
        //file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - AdminController constructed' . PHP_EOL, FILE_APPEND);
    }
    
    public function dashboard() {
        $stats = [
            'articles_count' => count($this->articleModel->getAllArticles()),
        ];
        
    }
    public function login(){
        Log::info(' - Login method called' . PHP_EOL, FILE_APPEND);
        
        try{
            if ($this->auth->isLoggedIn()){
                $this->auth->checkSessionTimeout();
                Log::info(' - User already logged in: ' . ($_SESSION['user']['full_name'] ?? 'unknown') . PHP_EOL, FILE_APPEND);
                
                return $this->auth->isLoggedIn() ?
                    ['success' => true, 'message' => 'login successful' ] :
                    ['success' => false, 'message' => 'session timeout'];
            } elseif($_POST){
                Log::info(' - Processing login for: ' . ($_POST['full_name'] ?? 'unknown') . PHP_EOL, FILE_APPEND);
                
                $response = $this->model->grantLogin($_POST);
                if($response['success']){
                    $userInfo = $this->userModel->getUserDetails($_POST['full_name'], $_POST['email']);
                    //file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - Admin model granted login to ' . $userInfo['full_name'] . PHP_EOL, FILE_APPEND);
                    
                    $this->auth->login($userInfo);
                    
                    // Проверяем, что сессия действительно создалась
                    //file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - Session ID after login: ' . session_id() . PHP_EOL, FILE_APPEND);
                    //file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - Session data: ' . print_r($_SESSION, true) . PHP_EOL, FILE_APPEND);
                    
                    return ['success' => true, 'message' => 'login successful'];
                } else {
                    return $response;
                }
            } else {
                return ['success' => false,'message' => 'missing parameters'];
            }
        } catch(Exception $e){
            Log::error($e->getMessage() . PHP_EOL);
            return ['success' => false,'message' => ''];
        }
    }
    /**
     * validates user login and redirects to login if not logged in or session expired. doesnt return anything
     * @return void
     */
    public function requireLogin(){
        $this->auth->requireLogin();
    }

    public function register(){
        if($_POST){
            $data = $_POST;
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $result = $this->model->registerUser($data);
            return $result;
        }
        return ['success' => false, 'message' => 'missing parameters'];
    }
    public function logout(){
        $this->auth->logout();
    }
    
    public function getGoogleAuthUrl() {
        // Check if Google auth config exists - try multiple paths
        $googleConfigPaths = [
            dirname(BASE_DIR) . '/config/google-auth.php',
            dirname(dirname(BASE_DIR)) . '/config/google-auth.php',
            BASE_DIR . '/../config/google-auth.php',
            BASE_DIR . '/../../config/google-auth.php'
        ];

        $googleConfigLoaded = false;
        foreach ($googleConfigPaths as $googleConfigPath) {
            if (file_exists($googleConfigPath)) {
                require_once $googleConfigPath;
                $googleConfigLoaded = true;
                break;
            }
        }

        if (!$googleConfigLoaded) {
            error_log("Google auth config file not found. Checked paths: " . implode(", ", $googleConfigPaths));
            return ['error' => 'Google authentication not configured - config file missing'];
        }

        // Rest of the method remains the same...
        // Validate that required constants are defined
        if (!defined('GOOGLE_CLIENT_ID') || empty(GOOGLE_CLIENT_ID) || GOOGLE_CLIENT_ID === 'your-google-client-id.apps.googleusercontent.com') {
            return ['error' => 'Google OAuth configuration missing or not set'];
        }

        if (!defined('GOOGLE_REDIRECT_URI') || empty(GOOGLE_REDIRECT_URI)) {
            return ['error' => 'Google redirect URI not configured'];
        }

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'access_type' => 'online',
            'prompt' => 'consent'
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        return ['auth_url' => $authUrl];
    }

    public function emailRegister($email){
        $this->model->addEmailSubscriber($email);
    }

}
?>