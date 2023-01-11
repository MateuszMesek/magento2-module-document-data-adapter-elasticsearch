<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver;

use Generator;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class CompositeResolver implements DocumentNodesResolverInterface
{
    private TMap $resolvers;

    public function __construct(
        TMapFactory $TMapFactory,
        array $resolvers = []
    )
    {
        $this->resolvers = $TMapFactory->createSharedObjectsMap([
            'type' => DocumentNodesResolverInterface::class,
            'array' => $resolvers
        ]);
    }

    public function resolve(): Generator
    {
        $existNodeByPath = [];

        foreach ($this->resolvers as $resolver) {
            /** @var \MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver\DocumentNodesResolverInterface $resolver */
            foreach ($resolver->resolve() as $node) {
                /** @var \MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeInterface $node */
                $path = $node->getPath();

                if (isset($existNodeByPath[$path])) {
                    continue;
                }

                $existNodeByPath[$path] = true;

                yield $node;
            }
        }
    }
}
