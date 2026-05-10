<?php
namespace Thunderpc\Vkurse\Models;

require_once __DIR__ . '/../../config/servers.php';
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use Thunderpc\Vkurse\Utils\Log;

Log::init();
class Elasticsearch{
    protected static $sharedClient = null;
    public $esClient;
    public function __construct(){
        if(!self::$sharedClient){
            self::$sharedClient = $this->makeConnection();
        }

        $this->esClient = self::$sharedClient;
    }

    private function makeConnection(){
        try {
            $client = ClientBuilder::create()
                ->setHosts([ELASTIC_HOST])
                ->setRetries(2)
                ->build();
            
            // Проверяем соединение
            $info = $client->info();
            Log::info("Connected to Elasticsearch version: " . $info['version']['number']);
            return $client;
        } catch (\Exception $e) {
            Log::error("Failed to connect to Elasticsearch: " . $e->getMessage());
            return null;
        }
        return null;
    }

    public function search($query, $index, $fields, $page=1, $size=20){
        try {
            $response = $client->search([
                'index' => $index,
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => $fields,
                        ],
                    ],
                    'from' => ($page - 1) * $size,
                    'size' => $size,
                ],
            ]);
            $results = [];
            foreach (($response['hits']['hits'] ?? []) as $hit) {
                $results[] = array_merge(['id' => $hit['_id']], $hit['_source'] ?? []);
            }

            return [
                'total' => $response['hits']['total']['value'] ?? 0,
                'page' => $page,
                'per_page' => $size,
                'data' => $results,
            ];
        } catch (\Throwable $e) {
            Log::error("error during search {$e->getMessage()}");
            return [
                'total' => 0,
                'message' => 'Server error'
            ];
        }
        return [
            'total' => 0,
            'message' => 'Unknown error'
        ];
    }
}
?>