<?php
namespace Thunderpc\Vkurse\Admin\Controllers;
// require_once ROOT_PATH . '/../models/NewsModel.php';
// require_once ROOT_PATH . '/../models/ImageModel.php';
// require_once ROOT_PATH . '/../utils/FileUpload.php';
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\ArticleModel;
use Thunderpc\Vkurse\Models\UserModel;
use Thunderpc\Vkurse\Models\CategoryModel;
use Thunderpc\Vkurse\Models\ImageModel;
use Thunderpc\Vkurse\Utils\Utils;
use Thunderpc\Vkurse\Utils\Log;

Log::init();
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
        $this->imageModel = new ImageModel();
    }
    
    public function index() {
        $articles = $this->model->getAllArticles();
        return $articles;
    }
    
    public function create() {
        $imagePath;
        if ($_POST) {
            try {
                //$newsImageId = null;
                $authorId = $this->userModel->getAuthorId($_SESSION['user']['full_name'], $_SESSION['user']['email']);
                
                if($authorId <= 0){
                    return ['success' => false, 'message' => 'User is not registered as author'];
                }
                // Загрузка изображения новости
                if (!empty($_FILES['news_image']['name'])) {
                    $imageInfo = $this->utils->upload(IMAGES_PATH, IMAGETYPES, $_FILES['news_image']);
                    $imagePath = $imageInfo['filepath'];
                } else {
                    Log::error("cover image not set");
                }

                $catId = $this->categoryModel->getCategoryId($_POST['category']);
                if(!$catId){
                    $catId = $this->categoryModel->createCategory($_POST['category'])['category_id'];
                }
                
                $data = [
                    'author_id' => $authorId,
                    'category_id' => $catId,
                    'title' => $_POST['title'],
                    'content' => $_POST['content'],
                    'cover_image_url' => $imagePath ?? '',
                    'status' => $_POST['status'],
                    'category' => $_POST['category']
                ];
                
                $result = $this->model->createArticle($data);
                if ($result['success']) {
                    Log::info("article successfully created. id: {$result['id']}");
                    // SEND notifications
                    //sendOSPushNotification('new article', $data['newsTitle'], '/?p=actuality&id=' . $result['id']);
                    //header('Location: ?action=news&success=1');
                    //exit;
                } else {
                    if($imagePath) $this->utils->deleteFile($imagePath);
                }
                return $result;
            } catch (\Exception $e) {
                Log::error("article create - {$e->getMessage()}");
                if($imagePath) $this->utils->deleteFile($imagePath);
                return [
                    'success' => false,
                    'message' => 'Server error'
                ];
            }
        } else {
            Log::warn("no post fields for article create");
        }
        
    }
    
    public function edit($id) {
        $article = $this->model->getArticleById($id);
        
        if ($_POST) {
            try {
                $imagePath = $article['cover_image_url'];
                
                // Загрузка нового изображения
                // мы не сохраняем изображение в таблицу изображений. а вместо в таблице статьей
                if (!empty($_FILES['news_image']['name'])) {
                    $this->utils->deleteFile($imageInfo);
                    //$this->imageModel->deleteImage($imageInfo);
                    $imageInfo = $this->utils->upload(IMAGES_PATH, IMAGETYPES, $_FILES['news_image']);
                    $imagePath = $imageInfo['filepath'];
                    //$newsImageId = $this->imageModel->createImage($imageInfo['filepath'], $imageInfo['mime_type'], $aticle['author_id']);
                }
                
                $data = [
                    'title' => $_POST['title'],
                    'content' => $_POST['сontent'],
                    'cover_image_url' => $imagePath,
                    'category_id' => $_POST['category_id'],
                    'status' => $_POST['status'],
                    'new_title' => $_POST['title'] != $article['title']
                ];
                
                $result = $this->model->updateArticle($id, $data);
                if ($result) {
                    //$this->utils->sendOSPushNotification('new article', $data['newsTitle'], '/?p=actuality&id=' . $id);
                    
                }
                return ['success' => $result ];
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Server Error'
                ];
            }
        } else {
            return [
                'article' => $article
            ];
        }
        
    }
    
    public function delete($id) {
        
        return ['success' => $this->model->deleteArticle($id) ? true : false];
    }
    
}
?>