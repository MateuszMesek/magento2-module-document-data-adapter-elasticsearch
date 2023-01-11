<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;
use MateuszMesek\DocumentDataApi\Model\Data\DocumentDataInterface;

class BooleanType implements DataMapperInterface
{
    public function get(DocumentNodeInterface $documentNode, DocumentDataInterface $documentData): array
    {
        $path = $documentNode->getPath();
        $value = $documentData->get($path);

        if (null !== $value) {
            $value = (bool)(int)$value;
        }

        return [
            $path => $value
        ];
    }
}
