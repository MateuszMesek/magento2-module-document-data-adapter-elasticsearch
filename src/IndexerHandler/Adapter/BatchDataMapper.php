<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapperInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class BatchDataMapper implements BatchDataMapperInterface
{
    private string $documentName;
    private BatchDataMapperInterface $batchDataMapper;

    public function __construct(
        string $documentName,
        BatchDataMapperInterface $batchDataMapper
    )
    {
        $this->documentName = $documentName;
        $this->batchDataMapper = $batchDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function map(array $documentData, $storeId, array $context = [])
    {
        try {
            return $this->batchDataMapper->map($documentData, $storeId, ['entityType' => "document_data_$this->documentName"]);
        } catch (NoSuchEntityException $exception) {
            return $this->batchDataMapper->map($documentData, $storeId, ['entityType' => "document_data", 'documentName' => $this->documentName]);
        }
    }
}
