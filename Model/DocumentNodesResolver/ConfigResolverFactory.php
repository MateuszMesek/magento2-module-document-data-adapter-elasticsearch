<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver;

use Magento\Framework\ObjectManagerInterface;

class ConfigResolverFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly string $instanceName = ConfigResolver::class
    )
    {
    }

    public function create(string $documentName): DocumentNodesResolverInterface
    {
        return $this->objectManager->create(
            $this->instanceName,
            [
                'documentName' => $documentName
            ]
        );
    }
}
