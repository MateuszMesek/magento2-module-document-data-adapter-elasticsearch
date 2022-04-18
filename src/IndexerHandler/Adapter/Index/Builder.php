<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\Index;

use Magento\Elasticsearch\Model\Adapter\Index\BuilderInterface;

class Builder implements BuilderInterface
{
    public function build()
    {
        return [];
    }

    public function setStoreId($storeId)
    {

    }
}
