<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\BatchDataMapper;

use Elasticsearch\Serializers\SerializerInterface;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapperInterface;

class DefaultDataMapper implements BatchDataMapperInterface
{
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
    }

    public function map(array $documentData, $storeId, array $context = [])
    {
        foreach ($documentData as $documentId => $indexData) {
            foreach ($indexData as $indexKey => $indexValue) {
                if (!is_scalar($indexValue)) {
                    $indexValue = $this->serializer->serialize($indexValue);
                }

                $documentData[$documentId][$indexKey] = $indexValue;
            }
        }

        return $documentData;
    }
}
