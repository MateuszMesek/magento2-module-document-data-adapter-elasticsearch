<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;

class Auto implements FieldMapperInterface
{
    public function get(DocumentNodeInterface $documentNode): array
    {
        return [];
    }
}
