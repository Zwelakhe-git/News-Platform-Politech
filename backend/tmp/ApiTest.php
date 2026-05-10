<?php
namespace Thunderpc\Vkurse\Tests;

require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Api\NewsApi;
use Thunderpc\Vkurse\Admin\Models\AdminModel;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase{
    private $api;
    private $apiKey;
    private $hashedKey;
    private $responseFormat;
    protected function setUp(): void{
        $this->api = new NewsApi();
        $this->apiKey = preg_replace('/\W/', "_", uniqid('vukrse_', true));
        $this->hashedKey = md5($this->apiKey);
        $this->responseFormat = [
            'success'=> true,
            'message'=> "string",
            'data'=> [
                'count'=> 0,
                'category' => '',
                'source' => '',
                'date'=> date('Y-m-d H:i:s'),
                'articles'=> []
            ]
        ];
    }

    public function testFetchNews(): void{}

    public function testApiKeyCreate(): void{}

    public function testApiKeyAccept(): void{}
}
?>