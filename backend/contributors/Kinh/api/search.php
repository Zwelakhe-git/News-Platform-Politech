<?php
require_once __DIR__ . '/../bootstrap.php';

use Elasticsearch\ClientBuilder;

$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$size = 10;

if ($q === '') {
    jsonResponse(['error' => 'Query parameter q is required'], 400);
}

$client = ClientBuilder::create()
    ->setHosts([ELASTIC_HOST])
    ->build();

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

    jsonResponse([
        'total' => $response['hits']['total']['value'] ?? 0,
        'page' => $page,
        'per_page' => $size,
        'data' => $results,
    ]);
} catch (Throwable $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}