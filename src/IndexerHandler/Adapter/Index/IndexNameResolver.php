<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\Index;

use Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver as BaseIndexNameResolver;
use Magento\Elasticsearch\Model\Config;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class IndexNameResolver extends BaseIndexNameResolver
{
    private StoreManagerInterface $storeManager;
    private ?string $indexNamespace;
    private ?string $indexPattern;

    public function __construct(
        StoreManagerInterface $storeManager,

        ConnectionManager $connectionManager,
        Config $clientConfig,
        LoggerInterface $logger,
        $options = []
    )
    {
        $this->storeManager = $storeManager;
        $this->indexNamespace = $options['index_namespace'] ?? null;
        $this->indexPattern = $options['index_pattern'] ?? null;

        parent::__construct(
            $connectionManager,
            $clientConfig,
            $logger,
            $options
        );
    }

    /**
     * @inheritdoc
     */
    protected function getIndexNamespace()
    {
        return $this->indexNamespace ?? parent::getIndexNamespace();
    }

    /**
     * @inheritdoc
     */
    public function getIndexNameForAlias($storeId, $mappedIndexerId)
    {
        return $this->buildIndexName($storeId, $mappedIndexerId) ?? parent::getIndexNameForAlias($storeId, $mappedIndexerId);
    }

    /**
     * @inheritdoc
     */
    public function getIndexPattern($storeId, $mappedIndexerId)
    {
        $indexName = $this->buildIndexName($storeId, $mappedIndexerId);

        if (null === $indexName) {
            return parent::getIndexPattern($storeId, $mappedIndexerId);
        }

        return $indexName.'_v';
    }

    private function buildIndexName($storeId, $indexId): ?string
    {
        if (null === $this->indexPattern) {
            return null;
        }

        return strtr(
            $this->indexPattern,
            [
                '{namespace}' => $this->getIndexNamespace(),
                '{store_id}' => $storeId,
                '{store_code}' => $this->storeManager->getStore($storeId)->getCode(),
                '{index_id}' => $indexId
            ]
        );
    }

    public function getIndexFromAlias($storeId, $mappedIndexerId)
    {
        $storeIndex = '';
        $indexPattern = $this->getIndexPattern($storeId, $mappedIndexerId);
        $indexName = $this->getIndexNameForAlias($storeId, $mappedIndexerId);

        if ($this->client->existsAlias($indexName)) {
            $alias = $this->client->getAlias($indexName);
            $indices = array_keys($alias);

            foreach ($indices as $index) {
                if (strpos($index, (string) $indexPattern) === 0) {
                    $storeIndex = $index;
                    break;
                }
            }
        }

        return $storeIndex;
    }
}
