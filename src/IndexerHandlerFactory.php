<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch;

use Magento\Elasticsearch\Model\Indexer\IndexerHandler;
use Magento\Elasticsearch\Model\Indexer\IndexStructure;
use Magento\Framework\ObjectManagerInterface;
use MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter;
use MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\FieldMapper;
use MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\Index\IndexNameResolver;
use MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Config as ClientConfig;
use MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\BatchDataMapper;

class IndexerHandlerFactory
{
    private ObjectManagerInterface $objectManager;
    private Config $config;

    /**
     * @var IndexerHandler[]
     */
    private array $instances = [];

    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config
    )
    {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    public function create(string $documentName): IndexerHandler
    {
        $options = $this->config->getClientOptions();
        $clientConfig = $this->objectManager->create(
            ClientConfig::class,
            [
                'options' => $options
            ]
        );
        $indexNameResolver = $this->objectManager->create(
            IndexNameResolver::class,
            [
                'clientConfig' => $clientConfig,
                'options' => $options
            ]
        );

        $adapter = $this->objectManager->create(
            Adapter::class,
            [
                'fieldMapper' => $this->objectManager->create(
                    FieldMapper::class,
                    [
                        'documentName' => $documentName
                    ]
                ),
                'clientConfig' => $clientConfig,
                'indexNameResolver' => $indexNameResolver,
                'batchDocumentDataMapper' => $this->objectManager->create(
                    BatchDataMapper::class,
                    [
                        'documentName' => $documentName
                    ]
                ),
                'options' => $options
            ]
        );

        return $this->objectManager->create(
            IndexerHandler::class,
            [
                'indexStructure' => $this->objectManager->create(
                    IndexStructure::class,
                    [
                        'adapter' => $adapter
                    ]
                ),
                'adapter' => $adapter,
                'indexNameResolver' => $indexNameResolver,
                'data' => [
                    'indexer_id' => $documentName
                ]
            ]
        );
    }

    public function get(string $documentName): IndexerHandler
    {
        if (!isset($this->instances[$documentName])) {
            $this->instances[$documentName] = $this->create($documentName);
        }

        return $this->instances[$documentName];
    }
}
