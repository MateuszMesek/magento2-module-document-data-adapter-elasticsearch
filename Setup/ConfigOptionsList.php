<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Setup;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\Data\ConfigData;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Setup\ConfigOptionsListInterface;
use Magento\Framework\Setup\Option\TextConfigOption;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;

class ConfigOptionsList implements ConfigOptionsListInterface
{
    private const INPUT_DSN = 'document-data-elasticsearch-dsn';
    private const INPUT_INDEX_PATTERN = 'document-data-elasticsearch-index-pattern';

    public function getOptions()
    {
        return [
            new TextConfigOption(
                self::INPUT_DSN,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_DSN
            ),
            new TextConfigOption(
                self::INPUT_INDEX_PATTERN,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_INDEX_PATTERN
            ),
        ];
    }

    public function createConfig(array $options, DeploymentConfig $deploymentConfig)
    {
        $configData = new ConfigData(ConfigFilePool::APP_ENV);

        if (isset($options[self::INPUT_DSN])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_DSN, $options[self::INPUT_DSN]);
        }

        if (isset($options[self::INPUT_INDEX_PATTERN])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_INDEX_PATTERN, $options[self::INPUT_INDEX_PATTERN]);
        }

        return [$configData];
    }

    public function validate(array $options, DeploymentConfig $deploymentConfig)
    {
        return [];
    }
}
