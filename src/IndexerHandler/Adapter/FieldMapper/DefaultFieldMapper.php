<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter\FieldMapper;

use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use MateuszMesek\DocumentDataApi\Command\GetDocumentNodesInterface;

class DefaultFieldMapper implements FieldMapperInterface
{
    private GetDocumentNodesInterface $getDocumentNodes;

    public function __construct(
        GetDocumentNodesInterface $getDocumentNodes
    )
    {
        $this->getDocumentNodes = $getDocumentNodes;
    }

    public function getFieldName($attributeCode, $context = [])
    {
        return $attributeCode;
    }

    public function getAllAttributesTypes($context = [])
    {
        ['documentName' => $documentName] = $context;

        $nodes = $this->getDocumentNodes->execute($documentName);
        $types = [];

        foreach ($nodes as $node) {
            $types[$node['path']] = [
                'type' => 'text'
            ];
        }

        return $types;
    }
}
