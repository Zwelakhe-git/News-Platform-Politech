<?php
// Authentication configuration
require_once(__DIR__ . '/config.php');
session_start();

class Auth {
    
    public function __construct() {
        
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) || (isset($_SESSION['name']) && !empty($_SESSION['name']));
    }
    
    // Login user
    public function login($email, $password) {
        // password
        $valid_email = 'admin@konektem.net'; 
        $valid_password = 'adminkonektem';
        
        if ($email === $valid_email && $password === $valid_password) {
            $now = time();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['name'] = ADMIN_NAME;
            $_SESSION['login_time'] = $now;
            $_SESSION["sessionStart"] = $now;
            $_SESSION["sessionEnd"] = $now + 3600;
            $_SESSION["sessionId"] = session_id();
            $_SESSION["role"] = "admin";
            return true;
        }
        
        return false;
    }
    
    // Logout user
    public function logout() {
        session_destroy();
    }
    
    // Get current user
    public function getCurrentUser() {
        return $_SESSION['name'] ?? null;
    }
    public function guestStillAlive(){
        return ( isset($_SESSION['name']) && !empty($_SESSION['name']) );
    }
    
    // Require login - redirect if not logged in
    public function requireLogin($redirectTo = '/admin/login.php') {
        if (!$this->isLoggedIn()) {
            if($_SERVER['PHP_SELF'] == '/account/me/index.php'){
            	header('Location: /account/login.html');
            	exit;
            }
            header('Location: ' . $redirectTo);
            exit;
        }
    }
    
    // Check session timeout (optional security feature)
    //Admin
    public function checkSessionTimeout($timeoutMinutes = 60) {
        if (isset($_SESSION['login_time'])) {
            $elapsedTime = time() - $_SESSION['login_time'];
            if ($elapsedTime > ($timeoutMinutes * 60)) {
                header('Location: /admin/logout.php?timeout=1');
                exit;
            }
            // Update login time on activity
            $now = time();
            $_SESSION['login_time'] = $now;
            $_SESSION['sessionStart'] = $now;
        }
    }
    //Guests
    public function checkGuestSessionTimeout($timeoutMinutes = 60) {
        if (isset($_SESSION['login_time'])) {
            $elapsedTime = time() - $_SESSION['login_time'];
            if ($elapsedTime > ($timeoutMinutes * 60)) {
                $this->logout();
                return;
            }
            $_SESSION['login_time'] = time();
        }
    }
}
?>