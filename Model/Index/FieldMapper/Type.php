<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;

class Type implements FieldMapperInterface
{
    public function __construct(
        private readonly string $type
    )
    {
    }

    public function get(DocumentNodeInterface $documentNode): array
    {
        return [
            $documentNode->getPath() => [
                'type' => $this->type
            ]
        ];
    }
}
