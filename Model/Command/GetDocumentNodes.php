<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Command;

use Magento\Framework\Stdlib\ArrayManager;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeFactory;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver\ResolverPool;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper\Direct;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper\Auto;
use MateuszMesek\DocumentDataApi\Model\Command\GetDocumentNodesInterface;
use Traversable;

class GetDocumentNodes
{
    public function __construct(
        private readonly GetDocumentNodesInterface $getDocumentNodes,
        private readonly ResolverPool $resolverPool,
        private readonly ArrayManager $arrayManager,
        private readonly DocumentNodeFactory $documentNodeFactory
    )
    {
    }

    public function execute(string $documentName): Traversable
    {
        $documentNodes = $this->getDocumentNodes->execute($documentName);
        $elasticNodes = $this->resolverPool->get($documentName)->resolve();

        $elasticTree = [];

        foreach ($documentNodes as $documentNode) {
            $elasticTree = $this->arrayManager->set($documentNode->getPath(), $elasticTree, true);
        }

        foreach ($elasticNodes as $elasticNode) {
            if (!$this->arrayManager->exists($elasticNode->getPath(), $elasticTree)) {
                continue;
            }

            $elasticTree = $this->arrayManager->set(
                $elasticNode->getPath(),
                $elasticTree,
                $elasticNode
            );
        }

        foreach ($elasticTree as $path => $node) {
            yield $this->convertToNode($documentName, $path, $node);
        }
    }

    private function convertToNode(string $documentName, string $path, $node): DocumentNodeInterface
    {
        if ($node instanceof DocumentNodeInterface) {
            return $node;
        }

        return $this->documentNodeFactory->create([
            'documentName' => $documentName,
            'path' => $path,
            'fieldMapper' => Auto::class,
            'dataMapper' => Direct::class
        ]);
    }
}
