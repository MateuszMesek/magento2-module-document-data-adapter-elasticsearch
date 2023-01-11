<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;
use MateuszMesek\DocumentDataApi\Model\Data\DocumentDataInterface;

class FloatType implements DataMapperInterface
{
    public function get(DocumentNodeInterface $documentNode, DocumentDataInterface $documentData): array
    {
        $path = $documentNode->getPath();
        $value = $documentData->get($path);

        if (null !== $value) {
            $value = (float)$value;
        }

        return [
            $path => $value
        ];
    }
}
