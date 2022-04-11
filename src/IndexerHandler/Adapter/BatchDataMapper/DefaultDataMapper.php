<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\BatchDataMapper;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapperInterface;

class DefaultDataMapper implements BatchDataMapperInterface
{
    public function map(array $documentData, $storeId, array $context = [])
    {
        return $documentData;
    }
}
