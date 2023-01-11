<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Client\Adapter;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IndexNameResolverInterface;

abstract class AbstractHandler
{
    public function __construct(
        protected readonly IndexNameResolverInterface $indexNameResolver,
        protected readonly Adapter                    $adapter,
    )
    {
    }

    public function isAvailable(array $dimensions): bool
    {
        $indexName = $this->indexNameResolver->resolve($dimensions);

        return $this->adapter->existsIndex($indexName);
    }
}
