<?php
namespace Thunderpc\Vkurse\Auth;

class Auth {
    private $debugFile = __DIR__ . '/../../logs/log.log';
    // Check if user is logged in
    public function isLoggedIn() {
        return (isset($_SESSION['user']) && !empty($_SESSION['user']));
    }
    
    // Login user
    public function login($user) {
        session_start();
        $now = time();
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = $now;
        $_SESSION['session_start'] = $now;
        $_SESSION['session_end'] = $now + 3600;
        $_SESSION['session_id'] = session_id();
        file_put_contents($this->debugFile, 'session created for ' . $_SESSION['user']['full_name'] . PHP_EOL, FILE_APPEND);
        return true; 
    }
    
    // Logout user
    public function logout() {
        session_destroy();
    }
    
    // Get current user
    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    // Require login - redirect if not logged in
    public function requireLogin($redirectTo = '/vkurse/auth/login') {
        if (!$this->isLoggedIn()) {
            if(preg_match('/user/', $_GET['route'])){
            	header('Location: /vkurse/auth/login');
            	exit;
            }
            header('Location: ' . $redirectTo);
            exit;
        }
    }
    
    // Check session timeout (optional security feature)
    //Admin
    public function checkSessionTimeout($timeoutMinutes = 60) {
        if (isset($_SESSION['session_start'])) {
            $elapsedTime = time() - $_SESSION['session_start'];
            if ($elapsedTime > ($timeoutMinutes * 60)) {
                $this->logout();
            } else {
                // Update login time on activity
                $now = time();
                $_SESSION['session_start'] = $now;
            }
        }
    }

    public function acceptAPIKey($apiKey){
        // we need the pdo to access the database
    }
}
?>