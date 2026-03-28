<?php
namespace Thunderpc\Vkurse\Admin\Controllers;
// require_once ROOT_PATH . '/../models/NewsModel.php';
// require_once ROOT_PATH . '/../models/ImageModel.php';
// require_once ROOT_PATH . '/../utils/FileUpload.php';
require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\ArticleModel;
use Thunderpc\Vkurse\Admin\Models\UserModel;
use Thunderpc\Vkurse\Admin\Models\CategoryModel;
use Thunderpc\Vkurse\Admin\Utils\Utils;
//use Thunderpc\Vkurse\Admin\Utils\FileUploader;

class ArticleController {
    private $model;
    private $imageModel;
    private $utils;
    private $userModel;
    private $categoryModel;
    
    public function __construct() {
        $this->model = new ArticleModel();
        $this->utils = new Utils();
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
    }
    
    public function index() {
        $articles = $this->model->getAllArticles();
        //require_once ADMIN_DIR . '/../views/news/list.php';
    }
    
    public function create() {
        if ($_POST) {
            try {
                $newsImageId = null;
                $authorId = !$this->userModel->getAuthorId($_SESSION['user']['name'], $_SESSION['user']['email']);
                $imageInfo;
                
                // Загрузка изображения новости
                if (!empty($_FILES['news_image']['name'])) {
                    //$uploader = new FileUploader();
                    $imageInfo = $utils->upload(BASE_DIR . '/uploads/images/', IMAGETYPES, $_FILES['news_image']);
                    $newsImageId = $this->imageModel->createImage($imageInfo['filepath'], $imageInfo['mime_type']);
                }
                if($authorId <= 0){
                    //$authorId = $this->userModel->createUser($_SESSION['user']);
                    return ['success' => false, 'message' => 'User is not registered as author'];
                }

                // slug is handled by the model
                $data = [
                    'author_id' => $authorId,
                    'category_id' => $_POST['category_id'],
                    'title' => $_POST['title'],
                    'content' => $_POST['fullContent'],
                    'cover_image_url' => $imageInfo['filepath'],
                    'status' => $_POST['status']
                ];
                
                $result = $this->model->createNews($data);
                if ($result['success']) {
                    // SEND notifications
                    //sendOSPushNotification('new article', $data['newsTitle'], '/?p=actuality&id=' . $result['id']);
                    //header('Location: ?action=news&success=1');
                    //exit;
                }
                return $result;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        //require_once ADMIN_DIR . '/views/news/create.php';
    }
    
    public function edit($id) {
        $article = $this->model->getArticleById($id);
        
        if ($_POST) {
            try {
                $imageInfo = $article['cover_image_url'];
                
                // Загрузка нового изображения
                if (!empty($_FILES['news_image']['name'])) {
                    $imageInfo = $this->utils->upload(BASE_DIR . '/../uploads/images/', IMAGETYPES, $_FILES['news_image']);
                    //$newsImageId = $this->imageModel->createImage($imageInfo['filepath'], $imageInfo['mime_type']);
                }
                
                $data = [
                    'author_id' => $authorId,
                    'category_id' => $_POST['category_id'],
                    'title' => $_POST['title'],
                    'slug' => $slug,
                    'content' => $_POST['fullContent'],
                    'cover_image_url' => $imageInfo['filepath'],
                    'status' => $_POST['status']
                ];
                
                $result = $this->model->updateArticle($id, $data);
                if ($result) {
                    //$this->utils->sendOSPushNotification('new article', $data['newsTitle'], '/?p=actuality&id=' . $id);
                    //header('Location: /vkurse/user/me?success=1');
                    //exit;
                }
                return ['success' => $result ? true : false];
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        } else {
            // redirect
        }
        
        //require_once ADMIN_DIR . '/views/news/edit.php';
    }
    
    public function delete($id) {
        
        return ['success' => $this->model->deleteArticle($id) ? true : false];
    }
    
    // Метод для предпросмотра новости
    public function preview($id) {
        $news = $this->model->getArticleById($id);
        try{
            require_once ADMIN_DIR . '/views/news/preview.php';
        } catch(Exception $e){
            die($e->getMessage());
        }
        
    }
}
?>