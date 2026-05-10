<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;
use Elastic\Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()
    ->setHosts([ELASTIC_HOST])
    ->build();
if($client){
    echo "Connection established successfully" . PHP_EOL;
    $q = "title";
    $page = 1;
    $size = 20;
    try {
        $response = $client->search([
            'index' => 'news',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $q,
                        'fields' => ['title^3', 'content'],
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

        print_r([
            'total' => $response['hits']['total']['value'] ?? 0,
            'page' => $page,
            'per_page' => $size,
            'data' => $results,
        ]);
    } catch (Throwable $e) {
        print_r(['error' => $e->getMessage()]);
    }
} else {
    echo "Failed to connect to es server" . PHP_EOL;
}
?>