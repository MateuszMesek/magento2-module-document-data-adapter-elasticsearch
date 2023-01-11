<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver;

use InvalidArgumentException;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class ResolverPool
{
    private TMap $resolversByDocumentName;

    public function __construct(
        TMapFactory $TMapFactory,
        private readonly ConfigResolverFactory $configResolverFactory,
        array $documentNames = []
    )
    {
        $this->resolversByDocumentName = $TMapFactory->createSharedObjectsMap([
            'type' => DocumentNodesResolverInterface::class,
            'array' => $documentNames
        ]);
    }

    public function get(string $documentName): DocumentNodesResolverInterface
    {
        $resolver = $this->resolversByDocumentName[$documentName];

        if (null === $resolver) {
            $resolver = $this->configResolverFactory->create($documentName);
        }

        if (!$resolver instanceof DocumentNodesResolverInterface) {
            throw new InvalidArgumentException("Document data '$documentName' is without nodes");
        }

        return $resolver;
    }
}
