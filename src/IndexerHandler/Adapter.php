<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler;

use Magento\Elasticsearch\Model\Adapter\Elasticsearch as BaseAdapter;

class Adapter extends BaseAdapter
{
    public function addDocs(array $documents, $storeId, $mappedIndexerId)
    {
        #$this->checkIndex($storeId, $mappedIndexerId, true);

        return parent::addDocs($documents, $storeId, $mappedIndexerId);
    }
}
