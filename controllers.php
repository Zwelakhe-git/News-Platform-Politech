<?php
require_once __DIR__ . '/config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Controllers\AdminController;
use Thunderpc\Vkurse\Admin\Controllers\ArticleController;
use Thunderpc\Vkurse\Admin\Models\ArticleModel;
use Thunderpc\Vkurse\Api\NewsApi;
use Thunderpc\Vkurse\Api\UploadApi;
use Thunderpc\Vkurse\Admin\Utils\Log;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');
$dotenv->load();
Log::init();

/*spl_autoload_register(function ($class){
    if(file_exists(ADMIN_DIR . '/models/' . $class . '.php')){
        require_once ADMIN_DIR . '/models/' . $class . '.php';
    } elseif(file_exists(ADMIN_DIR . '/controllers/' . $class . '.php' )){
        require_once ADMIN_DIR . '/controllers/' . $class . '.php';
    }
});*/
function getScript($url, array $params = null, array $values = null){
    $result = [
        'url' => $url
    ];
    if(($params && $values) && (count($params) === count($values))){
        $result['params'] = array_combine($params, $values);
    }
    return $result;
}
class View{
    private $basePath;
    public function __construct($basePath){
        $this->basePath = $basePath;
    }

    public function render($template, $data = []){
        
        $filePath = $this->basePath . '/' . $template . '.php';
        if(!file_exists($filePath)){
            if(file_exists($template . '.php')){
                $filePath = $template . '.php';
            } else throw new Exception("Template [$filePath] not found");
        }
        extract($data);
        
        ob_start();

        require $filePath;

        return ob_get_clean();

    }
}

$view = new View(TEMPLATES_DIR);

$data = json_decode(file_get_contents(__DIR__ . '/api/mock-data.json'), true);

if(!$data){
    echo "Failed to read json data";
    exit;
}
$index = function ($req, $res) use ($view, $data) {
    $model = new ArticleModel();
    $articles = $model->getAllArticles('local');
    Log::info('local articles fetched');
    $context = [
        'news' => $data['news'],
        'styles' => [
            BASE_URL . '/static/css/news-page.css',
            BASE_URL . '/static/css/index.d7d100d6a36e2fb125b6.css'
        ],
        'scripts' => [getScript(BASE_URL . '/static/js/index.d876cf30aea7c0b6f820.js', ['defer'], ['']), ]
    ];
    // 'template' => TEMPLATES_DIR . '/news/actuality.php',

    $res->send($view->render('index', $context));
};

$news = function ($req, $res){
    //
    try{
        $file_path = BASE_DIR . '/html/news.html';
        $content = null;
        if($req->params->id){
            $content = "<h3>you passed id = {$req->params->id}</h3>";
        }
        if(file_exists($file_path)){
            $content = file_get_contents($file_path);
        } else {
            $content .= "<h1>ERROR 404</h1>";
        }
        $res->status(200);
        $res->send($content);
    } catch(Exception $e){
        Log::error($e->getMessage());
    }
};

$profile = function ($req, $res) use ($view){
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $controller = new AdminController();
    $controller->requireLogin();
    $section = isset($req->params->action) ? $req->params->action : 'profile';

    $title = '';
    $styles = [];

    //Log::info("opening page $section");
    switch($section){
        case "settings":
            $title = 'Настройки — spbVkurse';
            $styles = [BASE_URL . '/static/css/settings-page-mert.css'];
            break;
        case "inbox":
            $title = 'Сообщения — spbVkurse';
            $styles = [BASE_URL . '/static/css/inbox-page-mert.css'];
            break;
        default:
            $title = 'Профиль — spbVkurse';
            $styles = [ BASE_URL . '/static/css/profile-mert.css'];
            break;
    }
    $context = [
        'action' => $section,
        'title' => $title,
        'styles' => $styles,
        'template' => $section . ".php"
    ];
    $res->send($view->render('user/layout', $context));
    
};

$author_articles = function ($req, $res){
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $controller = new AdminController();
    $controller->requireLogin();
    $controller = new ArticleController();
    $all_articles = $controller->getAllArticles();
};

$view_author_admin = function ($req, $res) use ($view) {
    /**
     * handles get requests for users
     */
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $controller = new AdminController();//AdminController();
    $controller->requireLogin();
    
    $action = $req->params->action;
    $styles = [];
    $scripts = [];
    if($action === 'edit' || $action === 'create'){
        $scripts = array_merge($scripts,[
            getScript("https://cdn.tiny.cloud/1/{$_ENV['tinymce_api']}/tinymce/6/tinymce.min.js",['referrerpolicy'], ['origin']),
            // getScript(BASE_URL . '/static/js/tinymce-config.js'),
            getScript(BASE_URL . '/dist/index.16796132a0c23d48a61d.js', ['defer'],['']),
            getScript('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'),
        ]);
        $styles = array_merge($styles, [
            BASE_URL . '/dist/index.03fab93e8c21ad73f9a8.css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
        ]);
    }
    $context = [
        'user' => $_SESSION['user'],
        'action' => $action,
        'styles' => $styles,
        'scripts' => $scripts
    ];
    
    $res->send($view->render('user/layout', $context));
};

$author_admin = function ($req, $res){
    /**
     * handles post requests for creation, editing, and deleting.
     * first check if user is logged in or not
     */
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $result = [];
    try{
        $action =$req->params->action;
        $controller = new AdminController();
        $controller->requireLogin();
        $controller = new ArticleController();
        switch($action){
            case 'edit':
                $result = $controller->edit($_GET['id']);
                break;
            case 'create':
                $result = $controller->create();
                break;
            case 'delete':
                $result = $controller->delete($_GET['id']);
                break;
            default:
                break;
        }
    } catch(\Exception $e){
        Log::error($e->getMessage());
        $result = [
            'success' => false,
            'message' => 'Server error'
        ];
    }
    $res->json($result);
};

$authenticate = function ($req, $res){
    // Запускаем сессию ДО создания контроллера
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    Log::info(' - Authenticate called, session_id: ' . session_id());
    
    try{
        $controller = new AdminController();
        
        $result;
        if(preg_match('/register/', $_GET['route'] ?? '')){
            $result = $controller->register();
        } else {
            $result = $controller->login();
        }
        
        // Log::info(' - Result: ' . print_r($result, true));
        // Log::info('session arras: ' . print_r($_SESSION, true));
        
        $res->status(201)->json($result);
    } catch(Throwable $e){
        Log::error(' - Authentication Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        $res->json(['success' => false, 'message' => 'Server Error']);
    }
};

$login = function ($req, $res) use ($view) {
    $res->send($view->render('register'));
};
$logout = function ($req, $res) use ($view) {
    $controller = new AdminController();
    $controller->logout();
    $res->send($view->render('register'));
};

/** apis */
$news_api = function($req, $res){
    $api = new NewsApi();
    return $api->getArticles($req, $res);
};

$upload_api = function($req, $res){
    try{
        $api = new UploadApi();
        $action = $req->params->action;
        Log::info($action);

        if(isset($req->query->dest) && $req->query->dest === 'imgbb'){
            if($action === 'save'){
                return $api->uploadToImgbb($req, $res);
            } else if($action === 'delete'){
                return $api->deleteFromImgbb($req, $res);
            }
            
        }

        return $api->{$action . "File"}($req, $res);
    } catch(Exception $e){
        Log::error($e->getMessage());
        $res->json([
            'success' => false,
            'message' => 'invalid action'
        ]);
    }
};

$article_api = function($req, $res){
    $api = new \Thunderpc\Vkurse\Api\StatsApi();
    return $api->like($req, $res);
};

$comment_api = function($req, $res){
    $api = new \Thunderpc\Vkurse\Api\StatsApi();
    return $api->comment($req, $res);
};
?>