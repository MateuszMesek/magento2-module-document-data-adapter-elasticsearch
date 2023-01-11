<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;

interface FieldMapperInterface
{
    public function get(DocumentNodeInterface $documentNode): array;
}
