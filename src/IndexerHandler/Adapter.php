<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler;

use Magento\Elasticsearch\Model\Adapter\Elasticsearch as BaseAdapter;

class Adapter extends BaseAdapter
{
    public function addDocs(array $documents, $storeId, $mappedIndexerId)
    {
        $this->checkIndex($storeId, $mappedIndexerId, true);

        return parent::addDocs($documents, $storeId, $mappedIndexerId);
    }

    protected function getDocsArrayInBulkIndexFormat(
        $documents,
        $indexName,
        $action = self::BULK_ACTION_UPDATE
    ) {
        $bulkArray = [
            'index' => $indexName,
            'type' => $this->clientConfig->getEntityType(),
            'body' => [],
            'refresh' => true,
        ];

        foreach ($documents as $id => $document) {
            $bulkArray['body'][] = [
                $action => [
                    '_id' => $id,
                    '_type' => $this->clientConfig->getEntityType(),
                    '_index' => $indexName
                ]
            ];

            switch ($action) {
                case self::BULK_ACTION_INDEX:
                    $bulkArray['body'][] = $document;
                    break;

                case self::BULK_ACTION_UPDATE:
                    $bulkArray['body'][] = ['doc' => $document, 'doc_as_upsert' => true];
                    break;
            }
        }

        return $bulkArray;
    }
}
