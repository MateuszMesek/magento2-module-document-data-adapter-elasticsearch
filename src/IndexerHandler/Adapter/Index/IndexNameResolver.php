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
        $this->indexNamespace = $options['index'] ?: null;
        $this->indexPattern = $options['index_pattern'] ?: null;

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
        if (null !== $this->indexNamespace) {
            return $this->indexNamespace;
        }

        return parent::getIndexNamespace();
    }

    /**
     * @inheritdoc
     */
    public function getIndexPattern($storeId, $mappedIndexerId)
    {
        if (null === $this->indexPattern) {
            return parent::getIndexPattern($storeId, $mappedIndexerId);
        }

        return strtr(
            $this->indexPattern,
            [
                '{namespace}' => $this->getIndexNamespace(),
                '{store_id}' => $storeId,
                '{store_code}' => $this->storeManager->getStore($storeId)->getCode(),
                '{index_id}' => $mappedIndexerId
            ]
        );
    }
}
