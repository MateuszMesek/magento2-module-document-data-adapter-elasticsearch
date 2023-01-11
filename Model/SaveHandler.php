<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Client\Adapter;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Command\GetDocumentNodes;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper\DataMapperFactory;
use MateuszMesek\DocumentDataApi\Model\Data\DocumentDataInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IndexNameResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\SaveHandlerInterface;
use Traversable;

class SaveHandler extends AbstractHandler implements SaveHandlerInterface
{
    public function __construct(
        IndexNameResolverInterface                  $indexNameResolver,
        Adapter                                     $adapter,
        private readonly DimensionResolverInterface $documentNameResolver,
        private readonly GetDocumentNodes           $getDocumentNodes,
        private readonly DataMapperFactory          $dataMapperFactory
    )
    {
        parent::__construct(
            $indexNameResolver,
            $adapter
        );
    }

    public function saveIndex(array $dimensions, Traversable $documents): void
    {
        $indexName = $this->indexNameResolver->resolve($dimensions);
        $documentName = $this->documentNameResolver->resolve($dimensions);

        $documentNodes = iterator_to_array(
            $this->getDocumentNodes->execute($documentName)
        );

        $this->adapter->upsertDocuments(
            $indexName,
            $this->getDocumentsData($documents, $documentNodes)
        );
    }

    public function deleteIndex(array $dimensions, Traversable $documents): void
    {
        $indexName = $this->indexNameResolver->resolve($dimensions);

        $this->adapter->deleteDocuments(
            $indexName,
            $this->getDocumentsId($documents)
        );
    }

    /**
     * @param \Traversable $documents
     * @param array $dataMappers
     * @return \Traversable
     */
    private function getDocumentsData(Traversable $documents, array $dataMappers): Traversable
    {
        foreach ($documents as $documentId => $documentData) {
            yield $documentId => $this->getDocumentData($documentData, $dataMappers);
        }
    }

    private function getDocumentData(DocumentDataInterface $documentData, array $documentNodes): array
    {
        $data = [];

        foreach ($documentNodes as $documentNode) {
            $dataMapper = $this->dataMapperFactory->get($documentNode->getDataMapper());

            $fields = $dataMapper->get($documentNode, $documentData);

            foreach ($fields as $field => $value) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    /**
     * @param \Traversable $documents
     * @return \Traversable
     */
    private function getDocumentsId(Traversable $documents): Traversable
    {
        foreach ($documents as $documentId => $documentData) {
            yield $documentId => null;
        }
    }
}
