<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\DataInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\Config\IndexNamePatternInterface;

class Config implements IndexNamePatternInterface
{
    public const DEPLOYMENT_CONFIG_DSN = 'document_data/elasticsearch/dsn';
    public const DEPLOYMENT_CONFIG_INDEX_PATTERN = 'document_data/elasticsearch/index_pattern';

    public function __construct(
        private readonly DataInterface    $data,
        private readonly DeploymentConfig $deploymentConfig
    )
    {
    }

    public function getDocumentNodes(string $documentName): array
    {
        $nodes = $this->data->get("document/$documentName/nodes");

        if (null === $nodes) {
            $nodes = [];
        }

        return array_map(
            static function (array $node) use ($documentName) {
                $node['documentName'] = $documentName;

                return $node;
            },
            $nodes
        );
    }

    public function getIndexSettings(): array
    {
        return $this->data->get('index/settings');
    }

    public function getIndexSettingPathsByType(string $type): array
    {
        return array_keys(
            array_filter(
                $this->getIndexSettings(),
                static function (string $itemType) use ($type) {
                    return $itemType === $type;
                }
            )
        );
    }

    public function getDSN(): string
    {
        return $this->deploymentConfig->get(
            self::DEPLOYMENT_CONFIG_DSN,
            'https://localhost:9200?timeout=30'
        );
    }

    public function getIndexNamePattern(string $documentName): string
    {
        return $this->deploymentConfig->get(
            self::DEPLOYMENT_CONFIG_INDEX_PATTERN,
            'document_data_{{document_name}}_{{store_id}}'
        );
    }
}
