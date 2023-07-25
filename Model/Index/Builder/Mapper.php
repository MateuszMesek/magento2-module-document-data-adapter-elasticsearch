<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Builder;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Command\GetDocumentNodesInterface;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper\FieldMapperFactory;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DimensionResolverInterface;

class Mapper implements BuilderInterface
{
    public function __construct(
        private readonly DimensionResolverInterface $documentNameResolver,
        private readonly GetDocumentNodesInterface  $getDocumentNodes,
        private readonly FieldMapperFactory         $fieldMapperFactory,
        private readonly int                        $defaultMappingFieldsLimit = 1000
    )
    {
    }

    public function build(array $dimensions = []): array
    {
        $documentName = $this->documentNameResolver->resolve($dimensions);
        $documentNodes = $this->getDocumentNodes->execute($documentName);

        $result = [
            'mappings' => [
                '_meta' => [
                    'documentName' => $documentName,
                    'fields' => []
                ],
                'properties' => []
            ],
            'settings' => [
                'index' => [
                    'mapping' => [
                        'total_fields' => [
                            'limit' => $this->defaultMappingFieldsLimit
                        ]
                    ]
                ]
            ]
        ];

        foreach ($documentNodes as $documentNode) {
            $fieldMapper = $this->fieldMapperFactory->get($documentNode->getFieldMapper());

            $fields = $fieldMapper->get($documentNode);

            foreach ($fields as $fieldName => $fieldData) {
                $result['mappings']['properties'][$fieldName] = $fieldData;
            }

            unset($fields);
        }

        $result['mappings']['_meta']['fields'] = array_keys($result['mappings']['properties']);
        $result['settings']['index']['mapping']['total_fields']['limit'] += count($result['mappings']['properties']);

        return $result;
    }
}
