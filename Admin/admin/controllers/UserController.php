<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ImageModel.php';
require_once __DIR__ . '/../utils/FileUpload.php';


class UserController{
    private $model;
    private $imagemodel;
    
   public function __construct(){
       $this->model = new UserModel();
       $this->imagemodel = new ImageModel();
   }
    
   public function updateUserProfile() {
        // Check if user is logged in
        if (!isset($_SESSION['name'])) {
            logMessage('failed to update profile. not authenticated');
            return ['response' => 'fail', 'message' => 'Not authenticated'];
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['current_password'])) {
            return ['response' => 'fail', 'message' => 'Missing required fields'];
        }

        $username = $_SESSION['name'];
        $currentPassword = $input['current_password'];
        $newName = $input['name'] ?? $username;
        $newEmail = $input['email'] ?? '';
        $newAvatar = $input['avatar_url'] ?? '';
        $newPassword = $input['new_password'] ?? '';

        // Update profile via model
        $result = $this->model->updateUserProfile(
            $username,
            $currentPassword,
            $newName,
            $newEmail,
            $newAvatar,
            $newPassword
        );

        // Update session if username changed
        if ($result['response'] === 'success' && $newName !== $username) {
            $_SESSION['name'] = $newName;
        }

        return $result;
    }

    public function upadateAvatar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input){
                return ["success" => false, "message" => "failed to read data"];
            }
            
            return $this->model->updateUserAvatar($input);
        }
        return ['success' => false, 'message' => 'invalid request method'];
    }
    
    public function getUserProfile($username = null) {
        if (isset($_SESSION['name']) || $username) {
            logMessage('admin controller: getUserProfile', 'info');
            $userDetails = $this->model->getUserDetails($username ?? $_SESSION['name']);
            return [
                'response' => 'success',
                'name' => $userDetails['name'],
                'email' => $userDetails['email'],
                'avatar_url' => $userDetails['avatar_url'],
                'premiumnSubscription' => $userDetails['premium_subscription_status']
            ];
        }
        logMessage('admin controller: getUserProfile failed, ' . $username ?? $_SESSION['name']);
        return ['response' => 'fail', 'name' => 'guest', 'email' => '', 'avatar_url' => ''];
    }
}
?>