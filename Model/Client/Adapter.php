<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Client;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;
use Traversable;

class Adapter
{
    private const BULK_ACTION_DELETE = 'delete';
    private const BULK_ACTION_UPDATE = 'update';

    private ?Client $client = null;

    public function __construct(
        private readonly Config        $config,
        private readonly ClientFactory $clientFactory
    )
    {
    }

    public function getClient(): Client
    {
        if (null === $this->client) {
            $dsn = $this->config->getDSN();

            $this->client = $this->clientFactory->create($dsn);
        }

        return $this->client;
    }

    public function ping(): bool
    {
        try {
            return $this->getClient()->ping();
        } catch (NoNodesAvailableException $exception) {
            return false;
        }
    }

    public function getIndexBody(string $indexName): array
    {
        try {
            $response = $this->getClient()->indices()->get([
                'index' => $indexName
            ]);
        } catch (Missing404Exception) {
            return [];
        }

        return current($response);
    }

    public function createIndexBody(string $indexName, array $body): void
    {
        $this->getClient()->indices()->create([
            'index' => $indexName,
            'body' => $body
        ]);
    }

    public function updateIndexBody(string $indexName, array $body): void
    {
        $indies = $this->getClient()->indices();

        if (!empty($body['settings'])) {
            unset($body['settings']['index']['analysis']);

            $indies->putSettings([
                'index' => $indexName,
                'body' => $body['settings']
            ]);
        }

        if (!empty($body['mappings'])) {
            $indies->putMapping([
                'index' => $indexName,
                'body' => $body['mappings']
            ]);
        }
    }

    public function existsIndex(string $indexName): bool
    {
        return $this->getClient()->indices()->exists(['index' => $indexName]);
    }

    public function moveDocumentsBetweenIndexes(string $source, string $target): void
    {
        try {
            $this->getClient()->reindex([
                'body' => [
                    'source' => [
                        'index' => $source,
                    ],
                    'dest' => [
                        'index' => $target
                    ]
                ]
            ]);
        } catch (BadRequest400Exception) {

        }
    }

    public function updateAlias(string $aliasName, string $indexName): void
    {
        $indices = $this->getClient()->indices();
        $params = [
            'body' => [
                'actions' => []
            ]
        ];

        try {
            $response = $indices->getAlias(['name' => $aliasName]);

            foreach (array_keys($response) as $index) {
                $params['body']['actions'][] = ['remove' => ['alias' => $aliasName, 'index' => $index]];
            }
        } catch (Missing404Exception) {

        }

        $params['body']['actions'][] = ['add' => ['alias' => $aliasName, 'index' => $indexName]];

        $this->getClient()->indices()->updateAliases($params);
    }

    public function getIndexNameByAlias(string $aliasName): ?string
    {
        try {
            $response = $this->getClient()->indices()->getAlias(['name' => $aliasName]);

            return current(array_keys($response));
        } catch (Missing404Exception) {
            return null;
        }
    }

    public function upsertDocuments(string $indexName, Traversable $documents): void
    {
        $bulkUpsertDocuments = $this->getBulkQuery(
            $indexName,
            $documents,
            self::BULK_ACTION_UPDATE
        );

        $this->bulkQuery($bulkUpsertDocuments);
    }

    public function deleteDocuments(string $indexName, Traversable $documents): void
    {
        $bulkDeleteDocuments = $this->getBulkQuery(
            $indexName,
            $documents,
            self::BULK_ACTION_DELETE
        );

        $this->bulkQuery($bulkDeleteDocuments);
    }

    protected function getBulkQuery(
        $indexName,
        $documents,
        $action
    ): array
    {
        $query = [
            'index' => $indexName,
            'body' => [],
            'refresh' => true,
        ];

        foreach ($documents as $id => $document) {
            $query['body'][] = [
                $action => [
                    '_id' => $id,
                    '_index' => $indexName
                ]
            ];

            if ($action === self::BULK_ACTION_UPDATE) {
                $query['body'][] = ['doc' => $document, 'doc_as_upsert' => true];
            }
        }

        return $query;
    }

    private function bulkQuery(array $query): void
    {
        $this->getClient()->bulk($query);
    }

    public function getDocuments(string $indexName, array $body): Traversable
    {
        $query = [
            'index' => $indexName,
            'body' => array_merge(
                [
                    'sort' => [
                        '_id' => 'ASC'
                    ]
                ],
                $body
            )
        ];

        $paginateResponse = $this->paginateQuery($query);

        foreach ($paginateResponse as $response) {
            foreach ($response['hits']['hits'] as $hit) {
                yield $hit['_id'] => $hit['_source'];
            }
        }
    }

    private function paginateQuery(array $query): Traversable
    {
        $canPaginate = !isset($query['body']['from'], $query['body']['size']);
        $page = 1;
        $size = 100;

        do {
            $pageQuery = $this->getPageQuery($query, $page++, $size);

            $pageResponse = $this->getClient()->search($pageQuery);

            yield $pageResponse;

            if (empty($pageResponse['hits']['hits'])) {
                $canPaginate = false;
            }
        } while ($canPaginate);
    }

    private function getPageQuery(array $query, int $page, int $size): array
    {
        if (empty($query['body']['from'])) {
            $query['body']['from'] = ($page - 1) * $size;
        }

        if (empty($query['body']['size'])) {
            $query['body']['size'] = $size;
        }

        return $query;
    }
}
