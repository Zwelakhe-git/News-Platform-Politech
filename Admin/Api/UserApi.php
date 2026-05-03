<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\ArticleModel;
use Thunderpc\Vkurse\Admin\Models\AdminModel;
use Thunderpc\Vkurse\Auth\Auth;
use Thunderpc\Vkurse\Admin\Utils\Log;

Log::init();


class UseApi{
    /** for POST requests */
    public function saveProfile(){
        
    }

    public function updateProfile(){}
    public function sendEmail(){}
}