<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch;

use Magento\Elasticsearch\Model\Indexer\IndexerHandler;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Search\Request\DimensionFactory;
use MateuszMesek\DocumentDataIndexIndexerApi\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\SaveHandlerInterface;
use Traversable;

class SaveHandler implements SaveHandlerInterface
{
    private DimensionResolverInterface $documentNameResolver;
    private IndexerHandlerFactory $indexerHandlerFactory;
    private DimensionFactory $dimensionFactory;

    public function __construct(
        DimensionResolverInterface $documentNameResolver,
        IndexerHandlerFactory $indexerHandlerFactory,
        DimensionFactory $dimensionFactory
    )
    {
        $this->documentNameResolver = $documentNameResolver;
        $this->indexerHandlerFactory = $indexerHandlerFactory;
        $this->dimensionFactory = $dimensionFactory;
    }

    public function isAvailable(array $dimensions = []): bool
    {
        try {
            return $this->getIndexerHandler($dimensions)->isAvailable($dimensions);
        } catch (LocalizedException $exception) {
            return false;
        }
    }

    public function saveIndex(array $dimensions, Traversable $documents): void
    {
        $this->getIndexerHandler($dimensions)->saveIndex(
            $this->convertDimensions($dimensions),
            $this->getDocumentsData($documents)
        );
    }

    public function deleteIndex(array $dimensions, Traversable $documents): void
    {
        $this->getIndexerHandler($dimensions)->deleteIndex(
            $this->convertDimensions($dimensions),
            $this->getDocumentsId($documents)
        );
    }

    private function getIndexerHandler(array $dimensions): IndexerHandler
    {
        $documentName = $this->documentNameResolver->resolve($dimensions);

        return $this->indexerHandlerFactory->get($documentName);
    }

    private function convertDimensions(array $input): array
    {
        $output = [];

        foreach ($input as $name => $dimension) {
            $output[$name] = $this->dimensionFactory->create([
                'name' => $dimension->getName(),
                'value' => $dimension->getValue()
            ]);
        }

        return $output;
    }

    /**
     * @param \Traversable|\MateuszMesek\DocumentDataApi\Data\DocumentDataInterface[] $documents
     * @return \Traversable
     */
    private function getDocumentsData(Traversable $documents): Traversable
    {
        foreach ($documents as $documentId => $documentData) {
            yield $documentId => $documentData ? $documentData->getData() : null;
        }
    }

    /**
     * @param \Traversable $documents
     * @return \Traversable
     */
    private function getDocumentsId(Traversable $documents): Traversable
    {
        foreach ($documents as $documentId => $documentData) {
            yield $documentId;
        }
    }
}
