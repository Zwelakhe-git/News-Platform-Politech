<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\AdminModel;
use Thunderpc\Vkurse\Auth;
use Thunderpc\Vkurse\Utils\Log;
use Thunderpc\Vkurse\Models\UserModel;

Log::init();


class UserApi{
    /** for POST requests */
    public function saveProfile(){
        
    }

    public function getAllUsers($req, $res){
        $model = new UserModel();
        $users = $model->getAllUsers();
        return $res->status(200)->json([
            'success' => true,
            'count' => count($users),
            'users' => $users
        ]);
    }
    
    public function updateProfile(){}
    public function sendEmail(){}
}