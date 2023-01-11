<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper\Direct;

class DocumentNode implements DocumentNodeInterface
{
    public function __construct(
        private readonly string $documentName,
        private readonly string $path,
        private $fieldMapper,
        private $dataMapper = Direct::class
    )
    {
    }

    public function getDocumentName(): string
    {
        return $this->documentName;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFieldMapper(): callable|string
    {
        return $this->fieldMapper;
    }

    public function getDataMapper(): callable|string
    {
        return $this->dataMapper;
    }
}
