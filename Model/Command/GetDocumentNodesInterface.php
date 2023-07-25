<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Command;

use Traversable;

interface GetDocumentNodesInterface
{
    public function execute(string $documentName): Traversable;
}
