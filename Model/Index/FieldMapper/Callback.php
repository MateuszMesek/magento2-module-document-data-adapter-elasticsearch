<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper;

use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;

class Callback implements FieldMapperInterface
{
    private $callback;

    public function __construct(
        callable $callback
    )
    {
        $this->callback = $callback;
    }

    public function get(DocumentNodeInterface $documentNode): array
    {
        return call_user_func($this->callback, $documentNode);
    }
}
