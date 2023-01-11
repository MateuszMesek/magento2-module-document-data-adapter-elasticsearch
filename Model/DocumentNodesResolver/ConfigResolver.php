<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver;

use Generator;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeFactory;
use Traversable;

class ConfigResolver implements DocumentNodesResolverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly DocumentNodeFactory $documentNodeFactory,
        private readonly string $documentName
    )
    {
    }

    public function resolve(): Traversable
    {
        $nodes = $this->config->getDocumentNodes($this->documentName);

        foreach ($nodes as $node) {
            yield $this->documentNodeFactory->create($node);
        }
    }
}
