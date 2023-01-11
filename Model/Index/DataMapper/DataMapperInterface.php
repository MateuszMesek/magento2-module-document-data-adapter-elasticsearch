<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;
use MateuszMesek\DocumentDataApi\Model\Data\DocumentDataInterface;

interface DataMapperInterface
{
    public function get(DocumentNodeInterface $documentNode, DocumentDataInterface $documentData): array;
}
