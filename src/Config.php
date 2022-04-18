<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch;

use Magento\Framework\App\DeploymentConfig;

class Config
{
    public const DEPLOYMENT_CONFIG_HOSTNAME = 'document_data/elasticsearch/hostname';
    public const DEPLOYMENT_CONFIG_PORT = 'document_data/elasticsearch/port';
    public const DEPLOYMENT_CONFIG_INDEX_NAMESPACE = 'document_data/elasticsearch/index_namespace';
    public const DEPLOYMENT_CONFIG_INDEX_PATTERN = 'document_data/elasticsearch/index_pattern';
    public const DEPLOYMENT_CONFIG_INDEX_TYPE = 'document_data/elasticsearch/index_type';
    public const DEPLOYMENT_CONFIG_ENABLE_AUTH = 'document_data/elasticsearch/enable_auth';
    public const DEPLOYMENT_CONFIG_USERNAME = 'document_data/elasticsearch/username';
    public const DEPLOYMENT_CONFIG_PASSWORD = 'document_data/elasticsearch/password';
    public const DEPLOYMENT_CONFIG_TIMEOUT = 'document_data/elasticsearch/timeout';

    private DeploymentConfig $deploymentConfig;

    public function __construct(
        DeploymentConfig $deploymentConfig
    )
    {
        $this->deploymentConfig = $deploymentConfig;
    }

    public function getClientOptions(): array
    {
        return array_filter([
            'hostname' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_HOSTNAME),
            'port' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_PORT),
            'index_namespace' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_INDEX_NAMESPACE),
            'index_pattern' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_INDEX_PATTERN),
            'index_type' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_INDEX_TYPE),
            'enableAuth' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_ENABLE_AUTH),
            'username' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_USERNAME),
            'password' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_PASSWORD),
            'timeout' => $this->deploymentConfig->get(self::DEPLOYMENT_CONFIG_TIMEOUT),
        ]);
    }
}
