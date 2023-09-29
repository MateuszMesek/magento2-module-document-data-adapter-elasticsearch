<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Client\Adapter;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Builder\BuilderInterface;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Cleaner\CleanerInterface;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Differ\DifferInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IndexNameResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IndexStructureBuilderInterface;

class IndexStructureBuilder implements IndexStructureBuilderInterface
{
    public function __construct(
        private readonly IndexNameResolverInterface $indexNameResolver,
        private readonly BuilderInterface           $builder,
        private readonly DifferInterface            $differ,
        private readonly CleanerInterface           $cleaner,
        private readonly Adapter                    $adapter,

    )
    {
    }

    public function build(array $dimensions = []): void
    {
        $indexName = $this->indexNameResolver->resolve($dimensions);

        if (!$this->adapter->ping()) {
            return;
        }

        $new = $this->builder->build($dimensions);
        $current = $this->adapter->getIndexBody($indexName);

        $diff = $this->differ->check($new, $current);

        switch ($diff) {
            case DifferInterface::UPDATE:
                $this->update($indexName, $new);
                break;

            case DifferInterface::NEW:
                $this->create($indexName, $new);
                break;
        }
    }

    private function update(string $indexName, array $body): void
    {
        $body = $this->cleaner->clean($body);

        $this->adapter->updateIndexBody($indexName, $body);
    }

    private function create(string $aliasName, array $body): void
    {
        $currentIndexName = $this->adapter->getIndexNameByAlias($aliasName);
        $newIndexName = $this->findNewIndexName($aliasName);

        $this->adapter->createIndexBody($newIndexName, $body);

        if ($currentIndexName) {
            $this->adapter->moveDocumentsBetweenIndexes($currentIndexName, $newIndexName);
        }

        $this->adapter->updateAlias($aliasName, $newIndexName);

        if ($currentIndexName) {
            $this->adapter->closeIndex($currentIndexName);
        }
    }

    private function getVersionByIndexName(string $indexName): int
    {
        $version = 0;

        if (preg_match('~_v(\d)$~', $indexName, $matches)) {
            $version = (int)$matches[1];
        }

        return $version;
    }

    private function findNewIndexName(string $aliasName): string
    {
        $currentIndexName = $this->adapter->getIndexNameByAlias($aliasName);
        $version = 0;

        if (null !== $currentIndexName) {
            $version = $this->getVersionByIndexName($currentIndexName);
        }

        do {
            $newIndexName = $aliasName . '_v' . (++$version);
        } while ($this->adapter->existsIndex($newIndexName));

        return $newIndexName;
    }
}
