<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use MateuszMesek\DocumentData\Model\Data\DocumentDataFactory;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Client\Adapter;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IndexNameResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\ReadHandlerInterface;
use Traversable;

class ReadHandler extends AbstractHandler implements ReadHandlerInterface
{
    public function __construct(
        IndexNameResolverInterface $indexNameResolver,
        Adapter $adapter,
        private readonly DocumentDataFactory $documentDataFactory
    )
    {
        parent::__construct(
            $indexNameResolver,
            $adapter
        );
    }

    public function readIndex(array $dimensions, ?SearchCriteriaInterface $searchCriteria = null): Traversable
    {
        $indexName = $this->indexNameResolver->resolve($dimensions);
        $body = [];

        if (null !== $searchCriteria) {
            $this->addFilterToBody($searchCriteria, $body);
            $this->addPageToBody($searchCriteria, $body);
            $this->addSortToBody($searchCriteria, $body);
        }

        $documents = $this->adapter->getDocuments(
            $indexName,
            $body
        );

        foreach ($documents as $documentId => $documentData) {
            yield $documentId => $this->documentDataFactory->create($documentData);
        }
    }

    private function addFilterToBody(SearchCriteriaInterface $searchCriteria, array &$body): void
    {
        $query = [];

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            //TODO: convert filterGroup to elasticsearch query
        }

        if (empty($query)) {
            return;
        }

        $body['query'] = $query;
    }

    private function addPageToBody(SearchCriteriaInterface $searchCriteria, array &$body): void
    {
        $page = $searchCriteria->getCurrentPage();
        $size = $searchCriteria->getPageSize();

        if (null === $page && null === $size) {
            return;
        }

        $page = (int)$page;
        $size = (int)$size;

        $body['from'] = $size * ($page - 1);
        $body['size'] = $size;
    }

    private function addSortToBody(SearchCriteriaInterface $searchCriteria, array &$body): void
    {
        $sortOrders = $searchCriteria->getSortOrders();

        if (null === $sortOrders) {
            return;
        }

        $body['sort'] = [];

        foreach ($sortOrders as $sortOrder) {
            $body['sort'][] = [
                $sortOrder->getField() => strtolower($sortOrder->getDirection())
            ];
        }
    }
}
