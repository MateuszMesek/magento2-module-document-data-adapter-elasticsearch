<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Cleaner;

use Magento\Framework\Stdlib\ArrayManager;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;

class Settings implements CleanerInterface
{
    private const SETTINGS_PATH = 'settings';

    public function __construct(
        private readonly ArrayManager $arrayManager,
        private readonly Config       $config,
        private readonly string       $type
    )
    {
    }

    public function clean(array $body): array
    {
        $toRemove = $this->config->getIndexSettingPathsByType($this->type);

        foreach ($toRemove as $path) {
            $body = $this->arrayManager->remove(self::SETTINGS_PATH.'.'.$path, $body, '.');
        }

        return $body;
    }
}
