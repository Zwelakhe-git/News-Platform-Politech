<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\ArticleModel;
use Thunderpc\Vkurse\Admin\Models\AdminModel;
use Thunderpc\Vkurse\Auth\Auth;
use Thunderpc\Vkurse\Admin\Utils\Log;

Log::init();

/**
 * response format:
 * {
 * 'success': bool,
 * 'message': string,
 * 'data': {
 *  'count': int,
 *  'category': string,
 *  'source': string
 *  'date': timestamp,
 *  'articles': array
 * }
 * }
 */
class NewsApi{
    private $adminModel;
    private $model;
    public function __construct(){
        $this->adminModel = new AdminModel();
        $this->model = new ArticleModel();
        Log::info('NewsApi constructor called');
    }
    
    public function getArticles($req, $res){
        Log::info('calling getArticles');
        $cat = $req->params->category;
        $apiKey = $req->params->key;
        $quantity = $req->params->qty;
        //Log::info("received params: $cat, $apiKey, $quantity");

        if(!$this->adminModel->acceptAPIKey($apiKey)){
            $res->status(200)->json([
                'success' => false,
                'message' => 'invalid api key'
            ]);
            exit;
        }
        //Log::info("No error in admin model");
        $source = 'external';
        $articles = $this->model->getAllArticles($source);
        //$articles = array_filter($articles, fn($article) => $article['category'] === $cat);
        $articles = array_slice($articles, 0, $quantity);

        //$res->json(array_splice($res, 0, $quantity));
        $res->status(200)->json([
            'success'=> true,
            'message'=> 'test successful',
            'data'=> [
                'count'=> count($articles),
                'category' => $cat,
                'source' => $source,
                'date'=> date('Y-m-d H:i:s'),
                'articles'=> $articles
            ]
        ]);
    }
}

?>