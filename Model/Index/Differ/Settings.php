<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Differ;

use Magento\Framework\Stdlib\ArrayManager;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;

class Settings implements DifferInterface
{
    private const SETTINGS_PATH = 'settings';

    public function __construct(
        private readonly ArrayManager $arrayManager,
        private readonly Config       $config,
        private readonly string       $type,
        private readonly int          $result = self::NONE
    )
    {
    }

    public function check(array $new, array $current): int
    {
        $newSettings = $this->arrayManager->get(self::SETTINGS_PATH, $new, []);
        $currentSettings = $this->arrayManager->get(self::SETTINGS_PATH, $current, []);

        $paths = $this->config->getIndexSettingPathsByType($this->type);

        foreach ($paths as $path) {
            $newValue = $this->prepareValue(
                $this->arrayManager->get($path, $newSettings, delimiter: '.')
            );
            $currentValue = $this->prepareValue(
                $this->arrayManager->get($path, $currentSettings, delimiter: '.')
            );

            if ($newValue === $currentValue) {
                continue;
            }

            return $this->result;
        }

        return self::NONE;
    }

    private function prepareValue($value): string
    {
        $this->sortValue($value);

        return json_encode($value, JSON_NUMERIC_CHECK | JSON_THROW_ON_ERROR);
    }

    private function sortValue(&$value): void
    {
        if (!is_array($value)) {
            return;
        }

        ksort($value);

        foreach ($value as &$item) {
            $this->sortValue($item);
        }
    }
}
