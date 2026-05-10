<?php
namespace Thunderpc\Vkurse\Tests;

require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Models\ArticleModel;
use Thunderpc\Vkurse\Models\AdminModel;

use PHPUnit\Framework\TestCase;

class ArticleModelTest extends TestCase{
    private ArticleModel $model;
    private AdminModel $adminModel;

    protected function setUp(): void{
        $this->model = new ArticleModel();
        $this->adminModel = new AdminModel();
    }

    public function testLocalArticlesFetch(): void{
        try{
            $res = $this->model->getAllArticles('local');
            $this->assertTrue(is_array($res));
        } catch(Exception $e){
            $this->assertTrue(false, "Error {$e->getMessage()}");
        }
    }

    // public function testLoadArticles(): void{
    //     $sources = $this->adminModel->getAllExternalSources();
    //     if(empty($sources)){
    //         $this->assertTrue(false, "No external sources in DB");
    //     }
    //     $result = $this->model->loadArticlesFromExternalSource($sources[0]['id']);
    //     $this->assertTrue($result['success'], "Failed to load articles from source: {$source[0]['name']}");
    // }
}
?>