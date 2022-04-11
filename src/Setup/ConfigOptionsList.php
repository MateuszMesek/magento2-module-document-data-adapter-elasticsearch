<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Setup;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\Data\ConfigData;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Setup\ConfigOptionsListInterface;
use Magento\Framework\Setup\Option\FlagConfigOption;
use Magento\Framework\Setup\Option\TextConfigOption;
use MateuszMesek\DocumentDataAdapterElasticsearch\Config;

class ConfigOptionsList implements ConfigOptionsListInterface
{
    private const INPUT_HOSTNAME = 'document-data-elasticsearch-hostname';
    private const INPUT_PORT = 'document-data-elasticsearch-port';
    private const INPUT_INDEX = 'document-data-elasticsearch-index';
    private const INPUT_INDEX_PATTERN = 'document-data-elasticsearch-index-pattern';
    private const INPUT_ENABLED_AUTH = 'document-data-elasticsearch-enable-auth';
    private const INPUT_USERNAME = 'document-data-elasticsearch-username';
    private const INPUT_PASSWORD = 'document-data-elasticsearch-password';
    private const INPUT_TIMEOUT = 'document-data-elasticsearch-timeout';

    public function getOptions()
    {
        return [
            new TextConfigOption(
                self::INPUT_HOSTNAME,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_HOSTNAME
            ),
            new TextConfigOption(
                self::INPUT_PORT,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_PORT
            ),
            new TextConfigOption(
                self::INPUT_INDEX,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_INDEX
            ),
            new TextConfigOption(
                self::INPUT_INDEX_PATTERN,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_INDEX_PATTERN
            ),
            new FlagConfigOption(
                self::INPUT_ENABLED_AUTH,
                Config::DEPLOYMENT_CONFIG_ENABLE_AUTH
            ),
            new TextConfigOption(
                self::INPUT_USERNAME,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_USERNAME
            ),
            new TextConfigOption(
                self::INPUT_PASSWORD,
                TextConfigOption::FRONTEND_WIZARD_PASSWORD,
                Config::DEPLOYMENT_CONFIG_PASSWORD
            ),
            new TextConfigOption(
                self::INPUT_TIMEOUT,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                Config::DEPLOYMENT_CONFIG_TIMEOUT
            ),
        ];
    }

    public function createConfig(array $options, DeploymentConfig $deploymentConfig)
    {
        $configData = new ConfigData(ConfigFilePool::APP_ENV);

        if (isset($options[self::INPUT_HOSTNAME])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_HOSTNAME, $options[self::INPUT_HOSTNAME]);
        }

        if (isset($options[self::INPUT_PORT])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_PORT, (int)$options[self::INPUT_PORT]);
        }

        if (isset($options[self::INPUT_INDEX])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_INDEX, $options[self::INPUT_INDEX]);
        }

        if (isset($options[self::INPUT_INDEX_PATTERN])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_INDEX_PATTERN, $options[self::INPUT_INDEX_PATTERN]);
        }

        if (isset($options[self::INPUT_ENABLED_AUTH])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_ENABLE_AUTH, $options[self::INPUT_ENABLED_AUTH]);
        }

        if (isset($options[self::INPUT_USERNAME])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_USERNAME, $options[self::INPUT_USERNAME]);
        }

        if (isset($options[self::INPUT_PASSWORD])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_PASSWORD, $options[self::INPUT_PASSWORD]);
        }

        if (isset($options[self::INPUT_TIMEOUT])) {
            $configData->set(Config::DEPLOYMENT_CONFIG_TIMEOUT, (int)$options[self::INPUT_TIMEOUT]);
        }

        return [$configData];
    }

    public function validate(array $options, DeploymentConfig $deploymentConfig)
    {
        return [];
    }
}
