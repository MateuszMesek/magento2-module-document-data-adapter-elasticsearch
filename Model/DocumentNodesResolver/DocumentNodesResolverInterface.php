<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver;

use Traversable;

interface DocumentNodesResolverInterface
{
    /**
     * @return \Traversable
     */
    public function resolve(): Traversable;
}
