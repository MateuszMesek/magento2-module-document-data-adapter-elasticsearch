<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\IndexerHandler;

use Magento\AdvancedSearch\Model\Client\ClientResolver;
use Magento\Elasticsearch\Model\Config as BaseConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Search\EngineResolverInterface;

class Config extends BaseConfig
{
    private array $options;

    public function __construct(
        array $options,

        ScopeConfigInterface $scopeConfig,
        ClientResolver $clientResolver,
        EngineResolverInterface $engineResolver,
        $prefix = null,
        $engineList = []
    )
    {
        $this->options = $options;

        parent::__construct(
            $scopeConfig,
            $clientResolver,
            $engineResolver,
            $prefix,
            $engineList
        );
    }

    public function getIndexPrefix()
    {
        return $this->options['index_namespace'] ?? parent::getIndexPrefix();
    }

    public function getEntityType()
    {
        return $this->options['index_type'] ?? parent::getEntityType();
    }
}
