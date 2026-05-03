<?php
namespace Thunderpc\Vkurse\Auth;

class Auth {
    private $debugFile = __DIR__ . '/../../logs/log.log';
    private $sessionStarted = false;
    
    /**
     * Гарантированно запускает сессию, если она еще не запущена
     */
    private function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->sessionStarted = true;
        }
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        $this->ensureSession();
        $this->checkSessionTimeout();
        return (isset($_SESSION['user']) && !empty($_SESSION['user']));
    }
    
    // Login user
    public function login($user) {
        $this->ensureSession();
        $now = time();
        
        // Очищаем существующую сессию перед созданием новой
        session_regenerate_id(true);
        
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = $now;
        $_SESSION['session_start'] = $now;
        $_SESSION['session_end'] = $now + 3600;
        $_SESSION['session_id'] = session_id();
        
        //file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - Session created for ' . $_SESSION['user']['full_name'] . ' - ID: ' . session_id() . PHP_EOL, FILE_APPEND);
        
        return true;
    }
    
    // Logout user
    public function logout() {
        $this->ensureSession();
        
        // Полная очистка сессии
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - Session destroyed' . PHP_EOL, FILE_APPEND);
    }
    
    // Get current user
    public function getCurrentUser() {
        $this->ensureSession();
        return $_SESSION['user'] ?? null;
    }
    
    // Require login - redirect if not logged in
    public function requireLogin($redirectTo = '/vkurse/auth/login') {
        if (!$this->isLoggedIn()) {
            if(preg_match('/user/', $_GET['route'] ?? '')){
                header('Location: /vkurse/auth/login');
                exit;
            }
            header('Location: ' . $redirectTo);
            exit;
        }
    }
    
    // Check session timeout (optional security feature)
    public function checkSessionTimeout($timeoutMinutes = 60) {
        $this->ensureSession();
        
        if (isset($_SESSION['session_start'])) {
            $elapsedTime = time() - $_SESSION['session_start'];
            if ($elapsedTime > ($timeoutMinutes * 60)) {
                $this->logout();
                file_put_contents($this->debugFile, date('Y-m-d H:i:s') . ' - Session timeout after ' . $elapsedTime . ' seconds' . PHP_EOL, FILE_APPEND);
                return false;
            } else {
                // Update login time on activity
                $now = time();
                $_SESSION['session_start'] = $now;
                return true;
            }
        }
        return false;
    }
    
}