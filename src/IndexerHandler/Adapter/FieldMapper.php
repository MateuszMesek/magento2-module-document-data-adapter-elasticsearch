<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler\Adapter;

use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use Throwable;

class FieldMapper implements FieldMapperInterface
{
    private string $documentName;
    private FieldMapperInterface $fieldMapper;

    public function __construct(
        string $documentName,
        FieldMapperInterface $fieldMapper
    )
    {
        $this->documentName = $documentName;
        $this->fieldMapper = $fieldMapper;
    }

    public function getFieldName($attributeCode, $context = [])
    {
        try {
            return $this->fieldMapper->getFieldName($attributeCode, ['entityType' => "document_data_$this->documentName"]);
        } catch (Throwable $exception) {
            return $this->fieldMapper->getFieldName($attributeCode, ['entityType' => "document_data", 'documentName' => $this->documentName]);
        }
    }

    public function getAllAttributesTypes($context = [])
    {
        try {
            return $this->fieldMapper->getAllAttributesTypes(['entityType' => "document_data_$this->documentName"]);
        } catch (Throwable $exception) {
            return $this->fieldMapper->getAllAttributesTypes(['entityType' => "document_data", 'documentName' => $this->documentName]);
        }
    }
}
