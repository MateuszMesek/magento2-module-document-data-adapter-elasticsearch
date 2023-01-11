<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;
use MateuszMesek\DocumentDataApi\Model\Data\DocumentDataInterface;

class Callback implements DataMapperInterface
{
    private $callback;

    public function __construct(
        callable $callback
    )
    {
        $this->callback = $callback;
    }

    public function get(DocumentNodeInterface $documentNode, DocumentDataInterface $documentData): array
    {
        return call_user_func($this->callback, $documentNode, $documentData);
    }
}
